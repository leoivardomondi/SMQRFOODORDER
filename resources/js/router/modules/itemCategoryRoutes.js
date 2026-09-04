import ItemCategoryComponent from "../../components/admin/settings/ItemCategory/ItemCategoryComponent";
import ItemCategoryListComponent from "../../components/admin/settings/ItemCategory/ItemCateogryListComponent";
import ItemCategoryShowComponent from "../../components/admin/settings/ItemCategory/ItemCategoryShowComponent";

export default [
    {
        path: '/admin/item-categories',
        component: ItemCategoryComponent,
        name: 'admin.itemCategory',
        redirect: { name: 'admin.itemCategory.list' },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: 'items',
            breadcrumb: 'item_categories'
        },
        children: [
            {
                path: '',
                component: ItemCategoryListComponent,
                name: 'admin.itemCategory.list',
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: 'items',
                    breadcrumb: ''
                },
            },
            {
                path: "show/:id",
                component: ItemCategoryShowComponent,
                name: "admin.itemCategory.show",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "items",
                    breadcrumb: "view",
                },
            }
        ]
    },
    {
        path: '/admin/settings/item-categories',
        redirect: { name: 'admin.itemCategory.list' },
        name: 'admin.settings.itemCategory'
    },
    {
        path: '/admin/settings/item-categories/show/:id',
        redirect: to => ({ name: 'admin.itemCategory.show', params: { id: to.params.id } }),
        name: 'admin.settings.itemCategory.show'
    }
];
