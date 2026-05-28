import { readonly, ref } from 'vue';

type ConsentLevel = 'all' | 'essential';

const STORAGE_KEY = 'cookie_consent';

const storedConsent = typeof window !== 'undefined' ? (localStorage.getItem(STORAGE_KEY) as ConsentLevel | null) : null;

const consent = ref<ConsentLevel | null>(storedConsent);
const bannerVisible = ref<boolean>(storedConsent === null);

function loadAnalyticsScripts(): void {
    // Google Analytics — descomentar i substituir G-XXXXXXXXXX pel ID real
    // const script = document.createElement('script');
    // script.src = 'https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX';
    // script.async = true;
    // document.head.appendChild(script);
    // window.dataLayer = window.dataLayer || [];
    // function gtag(...args: unknown[]) { window.dataLayer.push(args); }
    // gtag('js', new Date());
    // gtag('config', 'G-XXXXXXXXXX');

    // Meta Pixel — descomentar i substituir XXXXXXXXXXXXXXX pel Pixel ID real
    // (function(f,b,e,v,n,t,s){...}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js'));
    // fbq('init', 'XXXXXXXXXXXXXXX');
    // fbq('track', 'PageView');
}

function acceptAll(): void {
    consent.value = 'all';
    localStorage.setItem(STORAGE_KEY, 'all');
    bannerVisible.value = false;
    loadAnalyticsScripts();
}

function rejectNonEssential(): void {
    consent.value = 'essential';
    localStorage.setItem(STORAGE_KEY, 'essential');
    bannerVisible.value = false;
}

function openBanner(): void {
    bannerVisible.value = true;
}

// Si l'usuari havia acceptat tot en una sessió anterior, carreguem els scripts
if (storedConsent === 'all') {
    loadAnalyticsScripts();
}

export function useCookieConsent() {
    return {
        consent: readonly(consent),
        bannerVisible: readonly(bannerVisible),
        acceptAll,
        rejectNonEssential,
        openBanner,
    };
}
