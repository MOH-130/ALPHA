import os, sys
from PyQt6.QtGui import QImage
from PyQt6.QtCore import QRect, Qt

sys.stdout.reconfigure(encoding='utf-8')

dest_dir = r"public/assets/images/platforms"
os.makedirs(dest_dir, exist_ok=True)

# Specification of each card:
# (key, source_file, crop_rect_percent (x%, y%, w%, h%), output_file)
cards_spec = [
    {
        "key": "xbox",
        "title_ar": "إكس بوكس (Xbox)",
        "title_en": "Xbox",
        "badge": "CONSOLE & PC",
        "accent": "#107c10",
        "icon": "🎮",
        "source": "public/uploads/products/prod_xbox_game_pass_ultimate.png",
        "crop": (0.0, 0.0, 1.0, 0.42),
        "output": "platform_xbox.webp"
    },
    {
        "key": "playstation",
        "title_ar": "بلايستيشن (PlayStation)",
        "title_en": "PlayStation",
        "badge": "PS5 & PS4",
        "accent": "#00439c",
        "icon": "🕹️",
        "source": "public/uploads/products/ps_plus.jpg",
        "crop": (0.0, 0.05, 1.0, 0.70),
        "output": "platform_playstation.webp"
    },
    {
        "key": "steam",
        "title_ar": "ستيم (Steam)",
        "title_en": "Steam",
        "badge": "PC & DECKS",
        "accent": "#00d2ff",
        "icon": "🚀",
        "source": "public/uploads/products/prod_steam_120_games.png",
        "crop": (0.0, 0.0, 1.0, 0.45),
        "output": "platform_steam.webp"
    },
    {
        "key": "pc_gaming",
        "title_ar": "ألعاب الكمبيوتر (PC Gaming)",
        "title_en": "PC Gaming",
        "badge": "AAA TITLES",
        "accent": "#ff007f",
        "icon": "💻",
        "source": "public/uploads/products/prod_steam_45k_games.png",
        "crop": (0.28, 0.0, 0.72, 0.65),
        "output": "platform_pc_gaming.webp"
    },
    {
        "key": "ea_sports_fc",
        "title_ar": "سلسلة EA SPORTS FC",
        "title_en": "EA SPORTS FC",
        "badge": "FC 27 / FC 26",
        "accent": "#00ff88",
        "icon": "⚽",
        "source": "public/uploads/products/prod_ea_fc_26_pc_steam.png",
        "crop": (0.0, 0.0, 1.0, 0.50),
        "output": "platform_ea_sports_fc.webp"
    },
    {
        "key": "minecraft",
        "title_ar": "ماين كرافت (Minecraft)",
        "title_en": "Minecraft Official",
        "badge": "JAVA & BEDROCK",
        "accent": "#22c55e",
        "icon": "⛏️",
        "source": "public/uploads/products/insta_11.png",
        "crop": (0.0, 0.0, 1.0, 0.60),
        "output": "platform_minecraft.webp"
    },
    {
        "key": "ubisoft",
        "title_ar": "يوبي سوفت (Ubisoft)",
        "title_en": "Ubisoft",
        "badge": "AC & SHOOTERS",
        "accent": "#0ea5e9",
        "icon": "⚔️",
        "source": "public/uploads/products/prod_ac4_black_flag_resynced.png",
        "crop": (0.0, 0.0, 1.0, 0.50),
        "output": "platform_ubisoft.webp"
    },
    {
        "key": "rockstar_games",
        "title_ar": "روكستار جيمز (Rockstar)",
        "title_en": "Rockstar Games",
        "badge": "GTA 6 & RDR 2",
        "accent": "#f59e0b",
        "icon": "⭐",
        "source": "public/uploads/products/gta6.png",
        "crop": (0.0, 0.0, 1.0, 0.75),
        "output": "platform_rockstar_games.webp"
    },
    {
        "key": "game_pass",
        "title_ar": "إكس بوكس جيم باس (Game Pass)",
        "title_en": "Xbox Game Pass",
        "badge": "450+ GAMES",
        "accent": "#107c10",
        "icon": "🟢",
        "source": "public/uploads/products/prod_xbox_game_pass_ultimate_features.png",
        "crop": (0.0, 0.0, 1.0, 0.48),
        "output": "platform_game_pass.webp"
    },
    {
        "key": "software",
        "title_ar": "برامج وتطبيقات (Software)",
        "title_en": "Creative Software",
        "badge": "ADOBE & TOOLS",
        "accent": "#8b5cf6",
        "icon": "🎨",
        "source": "public/uploads/products/prod_adobe_creative_cloud_1year_poster.png",
        "crop": (0.0, 0.0, 1.0, 0.46),
        "output": "platform_software.webp"
    },
    {
        "key": "iptv",
        "title_ar": "اشتراكات IPTV (Promax 4K)",
        "title_en": "IPTV 4K Streams",
        "badge": "BEIN & OSN & MOVIES",
        "accent": "#00d2ff",
        "icon": "📺",
        "source": "public/uploads/products/prod_promax_iptv_12m.png",
        "crop": (0.0, 0.0, 1.0, 0.52),
        "output": "platform_iptv.webp"
    },
    {
        "key": "subscriptions",
        "title_ar": "اشتراكات الترفيه و AI",
        "title_en": "Entertainment & AI",
        "badge": "NETFLIX & CHATGPT",
        "accent": "#e50914",
        "icon": "🍿",
        "source": "public/uploads/products/prod_netflix_4k_premium.png",
        "crop": (0.0, 0.0, 1.0, 0.55),
        "output": "platform_subscriptions.webp"
    }
]

target_w = 600
target_h = 400

print(f"Generating {len(cards_spec)} optimized platform cards...")
for spec in cards_spec:
    src_path = spec["source"]
    out_path = os.path.join(dest_dir, spec["output"])
    
    if not os.path.exists(src_path):
        print(f"ERROR: Source file not found: {src_path}")
        continue
    
    img = QImage(src_path)
    if img.isNull():
        print(f"ERROR: Failed to load image: {src_path}")
        continue
    
    orig_w = img.width()
    orig_h = img.height()
    
    cx, cy, cw, ch = spec["crop"]
    rx = int(orig_w * cx)
    ry = int(orig_h * cy)
    rw = int(orig_w * cw)
    rh = int(orig_h * ch)
    
    # Ensure within bounds
    rx = max(0, min(rx, orig_w - 10))
    ry = max(0, min(ry, orig_h - 10))
    rw = max(10, min(rw, orig_w - rx))
    rh = max(10, min(rh, orig_h - ry))
    
    cropped = img.copy(QRect(rx, ry, rw, rh))
    # Scale to 600x400 smoothly
    scaled = cropped.scaled(target_w, target_h, Qt.AspectRatioMode.KeepAspectRatioByExpanding, Qt.TransformationMode.SmoothTransformation)
    
    # Crop exact 600x400 center if expanded
    if scaled.width() > target_w or scaled.height() > target_h:
        off_x = (scaled.width() - target_w) // 2
        off_y = (scaled.height() - target_h) // 2
        final_img = scaled.copy(QRect(off_x, off_y, target_w, target_h))
    else:
        final_img = scaled
    
    # Save as WebP with high quality
    success = final_img.save(out_path, "WEBP", 88)
    if success:
        sz = os.path.getsize(out_path)
        print(f"  [OK] {spec['key']} -> {out_path} ({sz:,} bytes)")
    else:
        print(f"  [FAIL] Failed to save {out_path}")

print("Platform cards generation completed!")
