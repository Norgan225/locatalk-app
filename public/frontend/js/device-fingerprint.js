// Générer un fingerprint unique
async function getDeviceFingerprint() {
    const data = {
        userAgent: navigator.userAgent,
        language: navigator.language,
        platform: navigator.platform,
        screenResolution: `${screen.width}x${screen.height}`,
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        colorDepth: screen.colorDepth,
    };

    const jsonString = JSON.stringify(data);
    const encoder = new TextEncoder();
    const dataBuffer = encoder.encode(jsonString);
    const hashBuffer = await crypto.subtle.digest('SHA-256', dataBuffer);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
}

// Ajouter le fingerprint à tous les formulaires
document.addEventListener('DOMContentLoaded', async function() {
    const fingerprint = await getDeviceFingerprint();

    // Stocker dans sessionStorage
    sessionStorage.setItem('device_fingerprint', fingerprint);

    // Ajouter aux requêtes AJAX
    if (typeof axios !== 'undefined') {
        axios.defaults.headers.common['X-Device-Fingerprint'] = fingerprint;
    }

    // Ajouter aux formulaires
    document.querySelectorAll('form').forEach(form => {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'device_fingerprint';
        input.value = fingerprint;
        form.appendChild(input);
    });
});
