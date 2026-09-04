<template>
    <aside class="db-sidebar"
        :class="$route.path.includes('kitchen-display-system') || $route.path.includes('order-status-screen') ? 'hidden' : ''">
        <div class="db-sidebar-header">
            <router-link class="w-24" :to="{ name: 'frontend.home' }">
                <img :src="setting.theme_logo" alt="logo">
            </router-link>
            <button @click.prevent="handleSidebar" class="fa-solid fa-xmark xmark-btn close-db-menu"></button>
        </div>
        <!--        {{ menus }}-->
        <nav class="db-sidebar-nav">
            <ul class="db-sidebar-nav-list" v-if="menus.length > 0" v-for="menu in menus" :key="menu">
                <li class="db-sidebar-nav-item" v-if="menu.url === '#'" @click.prevent="sidebarActive($event)">
                    <a href="javascript:void(0);" class="db-sidebar-nav-title">
                        {{ $t('menu.' + menu.language) }}
                    </a>
                </li>

                <li class="db-sidebar-nav-item" v-else @click.prevent="sidebarActive($event)">
                    <router-link :to="'/admin/' + menu.url" class="db-sidebar-nav-menu">
                        <i class="text-sm" :class="menu.icon"></i>
                        <span class="text-base flex-auto">{{ $t('menu.' + menu.language) }}</span>
                    </router-link>
                </li>

                <li class="db-sidebar-nav-item" v-if="menu.children" v-for="children in menu.children"
                    @click.prevent="sidebarActive($event)">
                    <router-link :to="'/admin/' + children.url" class="db-sidebar-nav-menu">
                        <i class="text-sm" :class="children.icon"></i>
                        <span class="text-base flex-auto">{{ $t('menu.' + children.language) }}</span>
                    </router-link>
                </li>

                <template v-if="menu.language === 'users' && canManageEmployees">
                    <li class="db-sidebar-nav-item" @click.prevent="sidebarActive($event)">
                        <router-link to="/admin/branch-managers" class="db-sidebar-nav-menu">
                            <i class="text-sm lab lab-employee-2"></i>
                            <span class="text-base flex-auto">{{ $t('menu.branch_managers') }}</span>
                        </router-link>
                    </li>
                    <li class="db-sidebar-nav-item" @click.prevent="sidebarActive($event)">
                        <router-link to="/admin/pos-operators" class="db-sidebar-nav-menu">
                            <i class="text-sm lab lab-employee-2"></i>
                            <span class="text-base flex-auto">{{ $t('menu.pos_operators') }}</span>
                        </router-link>
                    </li>
                    <li class="db-sidebar-nav-item" @click.prevent="sidebarActive($event)">
                        <router-link to="/admin/stuff" class="db-sidebar-nav-menu">
                            <i class="text-sm lab lab-employee-2"></i>
                            <span class="text-base flex-auto">{{ $t('menu.stuff') }}</span>
                        </router-link>
                    </li>
                </template>
            </ul>
        </nav>
    </aside>
</template>

<script>
import appService from "../../../services/appService";

export default {
    name: "BackendMenuComponent",
    data: function () {
        return {
            activeParentId: 1,
            activeChildId: 0,
            sidebarOpen: false,
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        menus: function () {
            const rawMenus = JSON.parse(JSON.stringify(this.$store.getters.authMenu || []));
            if (!rawMenus.length) return [];

            let dashboard = null;
            let items = null;
            let categories = null;
            let attributes = null;

            const extractItem = (url) => {
                let found = null;
                const topIndex = rawMenus.findIndex(m => m.url === url);
                if (topIndex !== -1) {
                    found = rawMenus.splice(topIndex, 1)[0];
                } else {
                    for (let m of rawMenus) {
                        if (m.children && m.children.length) {
                            const childIndex = m.children.findIndex(c => c.url === url);
                            if (childIndex !== -1) {
                                found = m.children.splice(childIndex, 1)[0];
                                break;
                            }
                        }
                    }
                }
                return found;
            };

            dashboard = extractItem('dashboard');
            items = extractItem('items');
            categories = extractItem('item-categories');
            attributes = extractItem('item-attributes') || extractItem('settings/item-attributes');

            const result = [];
            if (dashboard) result.push(dashboard);
            if (items) result.push(items);

            if (categories) {
                if (!categories.icon) categories.icon = 'lab lab-item-categories';
                if (!categories.language) categories.language = 'item_categories';
                result.push(categories);
            } else {
                result.push({
                    name: 'Item Categories',
                    language: 'item_categories',
                    url: 'item-categories',
                    icon: 'lab lab-item-categories'
                });
            }

            if (attributes) {
                attributes.url = 'item-attributes';
                if (!attributes.icon) attributes.icon = 'lab lab-item-attributes';
                if (!attributes.language) attributes.language = 'attributes';
                result.push(attributes);
            } else {
                result.push({
                    name: 'Attributes',
                    language: 'attributes',
                    url: 'item-attributes',
                    icon: 'lab lab-item-attributes'
                });
            }

            for (let m of rawMenus) {
                if (m.url === '#' && m.children && m.children.length === 0) {
                    continue;
                }
                result.push(m);
            }

            return result;
        },
        sidebar() {
            return this.$store.getters['globalState/lists'].topSidebar;
        },
        canManageEmployees() {
            return appService.permissionChecker('employees') === true;
        },
    },
    mounted() {
        this.defaultSidebarActive();

    },
    methods: {
        sidebarActive: function (e) {
            const activeMenu = document.querySelector('.db-sidebar-nav-item.active');
            if (activeMenu) {
                activeMenu.classList.remove('active');
            }
            e?.currentTarget?.classList?.add('active');
            if (window.innerWidth < 1024 && e?.currentTarget?.querySelector('.db-sidebar-nav-menu')) {
                this.closeSidebar();
            }
        },
        defaultSidebarActive: function () {
            if (document?.querySelector(".db-sidebar-nav-menu")?.classList?.contains("active")) {
                document?.querySelector('.db-sidebar-nav-menu')?.parentElement?.classList?.add('active');
            } else {
                document?.querySelector('.router-link-exact-active')?.parentElement?.classList?.add('active');
            }
        },
        handleSidebar: function () {
            this.sidebarOpen = !this.sidebar;
            this.$store.dispatch("globalState/set", { topSidebar: this.sidebarOpen });

            if (document?.querySelector(".db-sidebar")?.classList?.contains("active")) {
                this.closeSidebar();
            } else {
                document?.querySelector(".db-sidebar")?.classList?.add("active");
                document?.querySelector(".db-main")?.classList?.add("expand");
                document?.querySelector(".db-sidebar-backdrop")?.classList?.add("active");
                document.body.style.overflow = "hidden";
            }
        },
        closeSidebar: function () {
            document?.querySelector(".db-main")?.classList?.remove("expand");
            document?.querySelector(".db-sidebar")?.classList?.remove("active");
            document?.querySelector(".db-sidebar-backdrop")?.classList?.remove("active");
            document.body.style.overflow = "";
            this.sidebarOpen = true;
            this.$store.dispatch("globalState/set", { topSidebar: this.sidebarOpen });
        },
    }
}
</script>
