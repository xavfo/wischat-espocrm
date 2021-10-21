Espo.define('wischat:views/admin/record/matching-configuration', 'views/record/base', function (Dep) {

    return Dep.extend({

        template: 'wischat:admin/record/matching-configuration',

        data: function () {
            var data = {};
            data.typeDataList = this.typeDataList;
            return data;
        },

        events: {
            'click .button-container [data-action="cancel"]': function () {
                this.actionCancel();
            },
            'click .button-container [data-action="save"]': function () {
                this.actionSave();
            }
        },

        setup: function () {
            Dep.prototype.setup.call(this);

            var model = this.model;
            var fieldTypeList = Espo.Utils.clone(this.getMetadata().get(['entityDefs', 'WischatProperty', 'matchingFieldTypeList'], []));
            var availableFieldList = this.availableFieldList = [];

            availableFieldList.sort(function (v1, v2) {
                return this.translate(v1, 'fields', 'WischatProperty').localeCompare(this.translate(v2, 'fields', 'WischatProperty'));
            }.bind(this));

            var fieldDefs = this.getMetadata().get(['entityDefs', 'WischatProperty', 'fields']) || {};
            for (var field in fieldDefs) {
                var item = fieldDefs[field];
                if (item.matchingDisabled) continue;

                var fieldType = item.type;
                if (~fieldTypeList.indexOf(fieldType)) {
                    availableFieldList.push(field);
                }
            }

            var typeList = this.typeList = this.getMetadata().get(['entityDefs', 'WischatProperty', 'fields', 'type', 'options']) || [];

            this.typeDataList = [];
            typeList.forEach(function (type) {
                var attribute = 'fieldList_' + type;
                var typeFieldList = Espo.Utils.clone(this.getMetadata().get(['entityDefs', 'WischatProperty', 'propertyTypes', type, 'fieldList']) || []);
                model.set(attribute, typeFieldList);
                var o = {
                    name: type,
                    fieldName: attribute,
                    fieldKey: attribute + 'Field',
                    labelText: this.translate(type, 'type', 'WischatProperty')
                };
                this.createField(attribute, 'views/fields/multi-enum', {
                    options: availableFieldList,
                    translation: 'WischatProperty.fields'
                }, 'edit');
                this.typeDataList.push(o);
            }, this);
        },

        actionSave: function () {
            Espo.Ui.notify(this.translate('pleaseWait', 'messages'));
            this.disableButtons();

            this.model.save().then(function () {
                this.getMetadata().load(function () {
                    this.getMetadata().storeToCache();
                    Espo.Ui.success(this.translate('Saved'));
                    this.enableButtons();
                }.bind(this), true);
            }.bind(this)).fail(function () {
                this.enableButtons();
            }.bind(this));
        },

        actionCancel: function () {
            this.getRouter().navigate('#Admin', {trigger: true});
        },

        enableButtons: function () {
            this.$el.find(".button-container button").removeAttr('disabled');
        },

        disableButtons: function () {
            this.$el.find(".button-container button").attr('disabled', 'disabled');
        }
    });
});