import { spawn } from 'child_process';
import http from 'http';

const CHROME_PATH = "C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe";
const BASE_URL = "http://127.0.0.1:8000";

const VIEWPORTS = [
    { name: 'Mobile S', width: 320, height: 568 },
    { name: 'Mobile M', width: 375, height: 667 },
    { name: 'Mobile L', width: 390, height: 844 },
    { name: 'Mobile XL', width: 430, height: 932 },
    { name: 'Tablet Portrait', width: 768, height: 1024 },
    { name: 'Tablet Landscape', width: 1024, height: 768 },
    { name: 'Laptop HD', width: 1366, height: 768 },
    { name: 'Desktop Full HD', width: 1920, height: 1080 }
];

const PAGES = [
    { name: 'Home', path: '/' },
    { name: 'Catalog', path: '/shop' },
    { name: 'Product', path: '/product/ea-sports-fc-25-pc' },
    { name: 'Cart', path: '/cart' },
    { name: 'Checkout', path: '/checkout' },
    { name: 'Login', path: '/login' },
    { name: 'Register', path: '/register' }
];

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function fetchJson(url) {
    return new Promise((resolve, reject) => {
        http.get(url, (res) => {
            let data = '';
            res.on('data', chunk => data += chunk);
            res.on('end', () => {
                try {
                    resolve(JSON.parse(data));
                } catch (e) {
                    reject(e);
                }
            });
        }).on('error', reject);
    });
}

class CDPClient {
    constructor(wsUrl) {
        this.wsUrl = wsUrl;
        this.ws = null;
        this.id = 1;
        this.callbacks = new Map();
    }

    connect() {
        return new Promise((resolve, reject) => {
            this.ws = new WebSocket(this.wsUrl);
            this.ws.onopen = () => resolve();
            this.ws.onerror = (err) => reject(err);
            this.ws.onmessage = (msg) => {
                const data = JSON.parse(msg.data);
                if (data.id && this.callbacks.has(data.id)) {
                    const cb = this.callbacks.get(data.id);
                    this.callbacks.delete(data.id);
                    if (data.error) cb.reject(data.error);
                    else cb.resolve(data.result);
                }
            };
        });
    }

    send(method, params = {}) {
        return new Promise((resolve, reject) => {
            const id = this.id++;
            this.callbacks.set(id, { resolve, reject });
            this.ws.send(JSON.stringify({ id, method, params }));
        });
    }

    async evaluate(expression) {
        const res = await this.send('Runtime.evaluate', {
            expression,
            returnByValue: true,
            awaitPromise: true
        });
        return res?.result?.value;
    }

    close() {
        if (this.ws) {
            this.ws.close();
        }
    }
}

async function main() {
    console.log('🚀 Launching Headless Chrome for Responsive Audit...');
    const chrome = spawn(CHROME_PATH, [
        '--headless=new',
        '--remote-debugging-port=9222',
        '--disable-gpu',
        '--no-sandbox',
        '--disable-extensions',
        '--window-size=1920,1080',
        'about:blank'
    ]);

    let version = null;
    for (let i = 0; i < 20; i++) {
        await sleep(500);
        try {
            version = await fetchJson('http://127.0.0.1:9222/json/version');
            break;
        } catch (e) {}
    }

    if (!version) {
        console.error('❌ Could not connect to Chrome debugging port.');
        chrome.kill();
        process.exit(1);
    }

    console.log('✅ Chrome connected:', version.Browser);

    const tabs = await fetchJson('http://127.0.0.1:9222/json/list');
    const targetTab = tabs.find(t => t.type === 'page') || tabs[0];
    const cdp = new CDPClient(targetTab.webSocketDebuggerUrl);
    await cdp.connect();

    await cdp.send('Page.enable');
    await cdp.send('DOM.enable');
    await cdp.send('CSS.enable');
    await cdp.send('Runtime.enable');

    await cdp.send('Page.addScriptToEvaluateOnNewDocument', {
        source: 'try { localStorage.setItem("alpha_welcome_seen", "true"); } catch(e) {}'
    });

    console.log('\n======================================================');
    console.log('        ALPHA STORE RESPONSIVE VIEWPORT AUDIT');
    console.log('======================================================\n');

    let totalTests = 0;
    let passedTests = 0;
    let failedTests = 0;
    const failures = [];

    for (const page of PAGES) {
        const fullUrl = `${BASE_URL}${page.path}`;
        console.log(`\n📄 Testing Page: ${page.name} (${page.path})`);

        // Navigate
        await cdp.send('Page.navigate', { url: fullUrl });
        await sleep(600);
        await cdp.evaluate('localStorage.setItem("alpha_welcome_seen", "true"); if (typeof closeWelcomeModal === "function") closeWelcomeModal();');

        for (const vp of VIEWPORTS) {
            totalTests++;
            // Set device metrics
            await cdp.send('Emulation.setDeviceMetricsOverride', {
                width: vp.width,
                height: vp.height,
                deviceScaleFactor: 1,
                mobile: vp.width <= 768
            });
            await sleep(350);

            // Audit Page
            const auditResult = await cdp.evaluate(`
                (() => {
                    const docEl = document.documentElement;
                    const body = document.body;
                    const winW = window.innerWidth;
                    const scrollW = Math.max(docEl.scrollWidth, body ? body.scrollWidth : 0);
                    const overflow = scrollW > winW + 1; // 1px subpixel margin

                    // Find overflowing elements
                    const overflowingEls = [];
                    if (overflow) {
                        const all = document.querySelectorAll('*');
                        for (let el of all) {
                            if (el.offsetWidth > 0 || el.offsetHeight > 0) {
                                const rect = el.getBoundingClientRect();
                                if (rect.right > winW + 2) {
                                    overflowingEls.push({
                                        tag: el.tagName.toLowerCase(),
                                        className: (el.className || '').toString().slice(0, 50),
                                        right: Math.round(rect.right),
                                        width: Math.round(rect.width)
                                    });
                                    if (overflowingEls.length >= 3) break;
                                }
                            }
                        }
                    }

                    // Check mobile hamburger menu visibility
                    const burger = document.querySelector('.mobile-menu-toggle');
                    const burgerStyle = burger ? window.getComputedStyle(burger).display : 'none';
                    const isBurgerExpected = winW <= 992;
                    const burgerCorrect = isBurgerExpected ? burgerStyle !== 'none' : burgerStyle === 'none';

                    // Check desktop nav visibility
                    const desktopNav = document.querySelector('.desktop-only-nav');
                    let desktopNavCorrect = true;
                    if (desktopNav) {
                        const dStyle = window.getComputedStyle(desktopNav).display;
                        desktopNavCorrect = isBurgerExpected ? dStyle === 'none' : dStyle !== 'none';
                    }

                    // Check drawer toggle on mobile
                    let drawerOpens = null;
                    if (winW <= 992 && burger) {
                        burger.click();
                        const drawer = document.querySelector('.mobile-nav-drawer');
                        const overlay = document.querySelector('.mobile-nav-overlay');
                        const drawerActive = drawer && drawer.classList.contains('active');
                        const overlayActive = overlay && overlay.classList.contains('active');
                        drawerOpens = drawerActive && overlayActive;

                        // Close it back
                        const closeBtn = document.querySelector('.mobile-drawer-close');
                        if (closeBtn) closeBtn.click();
                    }

                    return {
                        winW,
                        scrollW,
                        overflow,
                        overflowingEls,
                        burgerStyle,
                        burgerCorrect,
                        desktopNavCorrect,
                        drawerOpens
                    };
                })()
            `);

            if (!auditResult) {
                console.log(`  ❌ [${vp.name} - ${vp.width}px] Failed to evaluate`);
                failedTests++;
                failures.push({ page: page.name, vp: vp.name, error: 'Evaluation failed' });
                continue;
            }

            const isOk = !auditResult.overflow && auditResult.burgerCorrect && auditResult.desktopNavCorrect;
            if (isOk) {
                passedTests++;
                const extra = auditResult.drawerOpens !== null ? ` | Drawer toggle: ${auditResult.drawerOpens ? 'OK' : 'FAIL'}` : '';
                console.log(`  ✅ [${vp.name.padEnd(16)} ${String(vp.width).padStart(4)}px] No overflow (scrollW=${auditResult.scrollW}px) | Hamburger=${auditResult.burgerCorrect ? 'OK' : 'FAIL'}${extra}`);
            } else {
                failedTests++;
                console.log(`  ❌ [${vp.name.padEnd(16)} ${String(vp.width).padStart(4)}px] OVERFLOW: scrollW=${auditResult.scrollW}px > winW=${auditResult.winW}px`);
                if (auditResult.overflowingEls?.length > 0) {
                    console.log('     Offenders:', JSON.stringify(auditResult.overflowingEls));
                }
                failures.push({
                    page: page.name,
                    vp: vp.name,
                    width: vp.width,
                    scrollW: auditResult.scrollW,
                    winW: auditResult.winW,
                    offenders: auditResult.overflowingEls
                });
            }
        }
    }

    console.log('\n======================================================');
    console.log(`RESULTS: Total: ${totalTests} | Passed: ${passedTests} | Failed: ${failedTests}`);
    console.log('======================================================\n');

    if (failures.length > 0) {
        console.log('Failures detail:');
        console.log(JSON.stringify(failures, null, 2));
    }

    cdp.close();
    chrome.kill();
    process.exit(failedTests === 0 ? 0 : 1);
}

main().catch(err => {
    console.error('Fatal error:', err);
    process.exit(1);
});
