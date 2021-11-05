Espo.define('wischat:views/admin/wischat-settings', 'views/settings/record/edit', function (Dep) {
    return Dep.extend({
        detailLayout: [
            {
                label: '',
                rows: [
                    [
                        {name: "wischatApiKey"},
                        {name: "wischatSecretKey"},
                    ]
                ]
            }
        ],
        setup: function () {
            Dep.prototype.setup.call(this);
        },
        afterRender: function () {
            Dep.prototype.afterRender.call(this);
        }
    });
});

