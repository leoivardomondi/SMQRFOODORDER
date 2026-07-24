<template>
    <div class="row pt-2 md:pt-4" @click="closeSettingMenu($event)">
        <div class="col-12">
            <BreadcrumbComponent />
        </div>

        <div class="col-12 md:col-4 xl:col-3">
            <MenuComponent />
        </div>

        <div class="col-12 md:col-8 xl:col-9 pt-2 md:pt-4" id="setting-right-content">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
import MenuComponent from "./MenuComponent";
import BreadcrumbComponent from "../components/BreadcrumbComponent";
import appService from "../../../services/appService";

export default {
    name: "SettingsComponent",
    components: { MenuComponent, BreadcrumbComponent },
    watch: {
        $route() {
            this.scrollToTop();
        }
    },
    mounted() {
        this.scrollToTop();
    },
    methods: {
        closeSettingMenu: function (event) {
            return appService.closeSettingMenu(event);
        },
        scrollToTop: function () {
            const dbMain = document.querySelector('.db-main');
            if (dbMain) {
                dbMain.scrollTop = 0;
            }
            window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
            document.documentElement.scrollTop = 0;
            document.body.scrollTop = 0;
        }
    }
}
</script>