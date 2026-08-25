<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Translator;
use App\Services\CurrencyService;
use App\Services\AffiliateService;

class SetLocaleAndCurrencyMiddleware {
    public function handle(Request $request): ?Response {
        // Handle Language Switch
        $lang = $request->getQuery('lang');
        if ($lang && in_array($lang, ['ar', 'en'])) {
            Translator::setLocale($lang);
        } else {
            Translator::getLocale(); // Initialize from session/default
        }

        // Handle Currency Switch
        $curr = $request->getQuery('currency');
        if ($curr) {
            CurrencyService::setCurrency($curr);
        }

        // Handle Affiliate Referral Link
        $ref = $request->getQuery('ref');
        if ($ref) {
            AffiliateService::trackReferral($ref);
        }

        return null;
    }
}
