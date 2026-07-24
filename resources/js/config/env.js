const ENV = {
    API_URL: typeof APP_URL !== 'undefined' ? APP_URL : '',
    API_KEY: typeof APP_KEY !== 'undefined' ? APP_KEY : '',
    GOOGLE_MAP_KEY: typeof GOOGLE_TOKEN !== 'undefined' ? GOOGLE_TOKEN : '',
    DEMO: typeof APP_DEMO !== 'undefined' ? APP_DEMO : false
};
export default ENV;
