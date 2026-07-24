import {createI18n} from "vue-i18n";

function loadMessages() {
    const context = import.meta.glob('./languages/**/*.json', { eager: true });
    const messages = {};
    for (const key in context) {
        const match = key.match(/\/([a-z0-9-_]+)\.json$/i);
        if (match && match[1]) {
            const locale = match[1];
            messages[locale] = context[key].default || context[key];
        }
    }
    return {messages};
}

const {messages} = loadMessages();

const i18n = createI18n({
    legacy: false,
    locale: "en",
    fallbackLocale: "en",
    messages: messages
});

export default i18n;


