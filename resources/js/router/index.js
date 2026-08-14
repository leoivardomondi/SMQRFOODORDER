import { createRouter, createWebHistory } from "vue-router";
import ENV from '../config/env';
import appService from "../services/appService";
const DashboardComponent = () => import("../components/admin/dashboard/DashboardComponent.vue");
const EmployeeListComponent = () => import("../components/admin/employees/EmployeeListComponent.vue");
const NotFoundComponent = () => import("../components/frontend/otherPage/NotFoundComponent.vue");
const ExceptionComponent = () => import("../components/frontend/otherPage/ExceptionComponent.vue");
import store from "../store";
import roleEnum from "../enums/modules/roleEnum";
import authRoutes from "./modules/authRoutes";
import settingRoutes from "./modules/settingRoutes";
import offerRoutes from "./modules/offerRoutes";
import itemRoutes from "./modules/itemRoutes";
import couponRoutes from "./modules/couponRoutes";
import onlineOrderRoutes from "./modules/onlineOrderRoutes";
import pushNotificationRoutes from "./modules/pushNotificationRoutes";
import customerRoutes from "./modules/customerRoutes";
import administratorRoutes from "./modules/administratorRoutes";
import deliveryBoyRoutes from "./modules/deliveryBoyRoutes";
import employeeRoutes from "./modules/employeeRoutes";
import frontendRoutes from "./modules/frontendRoutes";
import salesReportRoutes from "./modules/salesReportRoutes";
import itemsReportRoutes from "./modules/itemsReportRoutes";
import posRoutes from "./modules/posRoutes";
import messageRoutes from "./modules/messageRoutes";
import profileRoutes from "./modules/profileRoutes";
import posOrderRoutes from "./modules/posOrderRoutes";
import kitchenDisplaySystemRoutes from "./modules/kitchenDisplaySystemRoutes";
import orderStatusScreenRoutes from "./modules/orderStatusScreenRoutes";
import transactionRoutes from "./modules/transactionRoutes";
import creditBalanceReportRoutes from "./modules/creditBalanceReportRoutes";
import subscriberRoutes from "./modules/subscriberRoutes";
import waiterRoutes from "./modules/waiterRoutes";
import chefRoutes from "./modules/chefRoutes";

const baseRoutes = [
    {
        path: "/",
        redirect: { name: "frontend.home" },
        name: "root"
    },
    {
        path: "/:pathMatch(.*)*",
        name: "route.notFound",
        component: NotFoundComponent,
        meta: {
            isFrontend: true,
        },
    },
    {
        path: "/exception",
        name: "route.exception",
        component: ExceptionComponent,
    },
    {
        path: "/admin/dashboard",
        component: DashboardComponent,
        name: "admin.dashboard",
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "dashboard",
            breadcrumb: "dashboard",
        },
    },
    {
        path: "/admin/branch-managers",
        component: EmployeeListComponent,
        name: "admin.branch-managers",
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "employees",
            breadcrumb: "branch_managers",
        },
    },
    {
        path: "/admin/pos-operators",
        component: EmployeeListComponent,
        name: "admin.pos-operators",
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "employees",
            breadcrumb: "pos_operators",
        },
    },
    {
        path: "/admin/stuff",
        component: EmployeeListComponent,
        name: "admin.stuff",
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "employees",
            breadcrumb: "stuff",
        },
    },
];

export const routes = baseRoutes.concat(
    frontendRoutes,
    authRoutes,
    settingRoutes,
    offerRoutes,
    itemRoutes,
    pushNotificationRoutes,
    couponRoutes,
    onlineOrderRoutes,
    customerRoutes,
    deliveryBoyRoutes,
    administratorRoutes,
    employeeRoutes,
    salesReportRoutes,
    itemsReportRoutes,
    messageRoutes,
    profileRoutes,
    posRoutes,
    posOrderRoutes,
    transactionRoutes,
    creditBalanceReportRoutes,
    subscriberRoutes,
    kitchenDisplaySystemRoutes,
    orderStatusScreenRoutes,
    waiterRoutes,
    chefRoutes
);

const permission = store.getters.authPermission;
appService.recursiveRouter(routes, permission);

const API_URL = ENV.API_URL;
const router = createRouter({
    linkActiveClass: "active",
    mode: 'history',
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { left: 0, top: 0 }
    }
});

// Start restored admin sessions on the Online Orders page.
// This resets when the app bundle loads, so refreshes and reopened app sessions
// return admins to incoming orders without affecting normal navigation.
let isInitialNavigation = true;

// Recover once when an open tab still references a route chunk from an older build.
router.onError((error, to) => {
    const message = String(error?.message || error);
    const isStaleChunk = /Failed to fetch dynamically imported module|Importing a module script failed|error loading dynamically imported module|Loading chunk .* failed/i.test(message);

    if (!isStaleChunk) {
        return;
    }

    const target = to?.fullPath || window.location.pathname + window.location.search;
    const reloadKey = 'bwibo-stale-chunk-reload';

    if (sessionStorage.getItem(reloadKey) !== target) {
        sessionStorage.setItem(reloadKey, target);
        window.location.assign(target);
    } else {
        sessionStorage.removeItem(reloadKey);
    }
});

router.afterEach(() => {
    sessionStorage.removeItem('bwibo-stale-chunk-reload');
    const dbMain = document.querySelector('.db-main');
    if (dbMain) {
        dbMain.scrollTop = 0;
    }
    window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
    document.documentElement.scrollTop = 0;
    document.body.scrollTop = 0;
});

router.beforeEach((to, from, next) => {
    const roleId = store.getters.authInfo?.role_id;
    const orderFirstRole = roleId === roleEnum.ADMIN || roleId === roleEnum.BRANCH_MANAGER;

    if (
        store.getters.authStatus &&
        orderFirstRole &&
        (isInitialNavigation || to.name === "admin.dashboard") &&
        to.name !== "admin.order.list"
    ) {
        isInitialNavigation = false;
        next({ name: "admin.order.list" });
        return;
    }

    isInitialNavigation = false;

    if (to.meta.riderOnly === true && store.getters.authInfo?.role_id !== roleEnum.DELIVERY_BOY) {
        next({ name: store.getters.authStatus ? "frontend.home" : "auth.login" });
        return;
    }

    if (to.meta.auth === true) {
        if (!store.getters.authStatus) {
            next({ name: "auth.login" });
        } else {
            if (to.meta.isFrontend === false) {
                if (to.meta.access === false) {
                    next({
                        name: "route.exception",
                    });
                } else {
                    next();
                }
            } else {
                next();
            }
        }
    } else if (to.name === "auth.login" && store.getters.authStatus) {
        next({ name: "frontend.home" });
    } else {
        next();
    }
});


export default router;
