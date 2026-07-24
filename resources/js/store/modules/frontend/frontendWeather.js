import axios from 'axios';

export const frontendWeather = {
    namespaced: true,
    state: {
        weather: {
            status: false,
            temp_c: 21,
            condition_text: '',
            icon: '☀️',
            fa_icon: 'fa-sun',
            is_raining: false,
            rain_advisory: null,
            branch_name: '',
            city: ''
        }
    },
    getters: {
        show: state => state.weather
    },
    actions: {
        show: function (context, branchId) {
            return new Promise((resolve, reject) => {
                const url = branchId ? `/api/frontend/weather/${branchId}` : `/api/frontend/weather`;
                axios.get(url).then((res) => {
                    context.commit('show', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    },
    mutations: {
        show: function (state, payload) {
            state.weather = payload;
        }
    }
};
