import axios from "axios";
import appService from "../../../services/appService";

let categoriesRequest = null;

export const frontendItemCategory = {
    namespaced: true,
    state: {
        lists: [],
        show: {},
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
        show: function (state) {
            return state.show;
        },
    },
    actions: {
        lists: function (context, payload) {
            const shouldStore = typeof payload?.vuex === "undefined" || payload.vuex === true;
            if (shouldStore && context.state.lists.length > 0 && !payload?.force) {
                return Promise.resolve({ data: { data: context.state.lists } });
            }
            if (shouldStore && categoriesRequest) {
                return categoriesRequest;
            }

            const request = new Promise((resolve, reject) => {
                let url = "frontend/item-category";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload?.vuex === "undefined" || payload.vuex === true) {
                        context.commit("lists", res.data.data);
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                }).finally(() => {
                    if (shouldStore) {
                        categoriesRequest = null;
                    }
                });
            });
            if (shouldStore) {
                categoriesRequest = request;
            }
            return request;
        },
        show: function (context, payload) {
            if(payload) {
                return new Promise((resolve, reject) => {
                    axios.get(`frontend/item-category/show/${payload.slug}`).then((res) => {
                        if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                            context.commit("show", res.data.data);
                        }
                        resolve(res);
                    }).catch((err) => {
                        reject(err);
                    });
                });
            }
        },
    },
    mutations: {
        lists: function (state, payload) {
            state.lists = payload;
        },
        show: function (state, payload) {
            state.show = payload;
        }
    },
};
