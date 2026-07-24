import axios from "axios";
import appService from "../../../services/appService";

let settingsRequest = null;

export const frontendSetting = {
    namespaced: true,
    state: {
        lists: [],
    },
    getters: {
        lists: function (state) {
            return state.lists;
        }
    },
    actions: {
        lists: function (context, payload) {
            if (context.state.lists && Object.keys(context.state.lists).length > 0 && !payload?.force) {
                return Promise.resolve({ data: { data: context.state.lists } });
            }
            if (settingsRequest) {
                return settingsRequest;
            }

            settingsRequest = new Promise((resolve, reject) => {
                let url = "frontend/setting";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    context.commit("lists", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                }).finally(() => {
                    settingsRequest = null;
                });
            });
            return settingsRequest;
        }
    },
    mutations: {
        lists: function (state, payload) {
            state.lists = payload;
        }
    },
};
