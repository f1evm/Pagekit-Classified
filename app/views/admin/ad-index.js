module.exports = {

    name: 'ad',

    el: '#ad',

    data: function() {
        return _.merge({
            ads: false,
            config: {
                filter: this.$session.get('ads.filter', {order: 'date desc', limit:25})
            },
            pages: 0,
            count: '',
            selected: [],
            canEditAll: false,
            categoryNames: []
        }, window.$data);
    },

    ready: function () {
        this.resource = this.$resource('api/classified/ad{/id}');
        this.$watch('config.page', this.load, {immediate: true});
    },

    watch: {

        'config.filter': {
            handler: function (filter) {
                if (this.config.page) {
                    this.config.page = 0;
                } else {
                    this.load();
                }

                this.$session.set('ads.filter', filter);
            },
            deep: true
        }

    },

    computed: {

        statusOptions: function () {

            var options = _.map(this.$data.statuses, function (status, id) {
                return { text: status, value: id };
            });

            return [{ label: this.$trans('Filter by'), options: options }];
        },

        categoryOptions: function () {
            var cN = [];
            var options = _.map(this.$data.categories, function (category, id) {
                cN.push({value: id, text: category.name});
                return { text: category.name, value: id };
            });
            this.categoryNames = cN;
            return [{ label: this.$trans('Filter by'), options: options }];
        },

        authors: function() {

            var options = _.map(this.$data.authors, function (author) {
                return { text: author.username, value: author.user_id };
            });

            return [{ label: this.$trans('Filter by'), options: options }];
        }
    },

    methods: {

      categoryName: function(catId){
        var catName = _.find(this.categoryNames,function(cN){return cN['value']==catId;});
        return catName ? catName['text'] : this.$trans('undefined');

      },


      active: function (ad) {
            return this.selected.indexOf(ad.id) != -1;
        },

        save: function (ad) {
            this.resource.save({ id: ad.id }, { ad: ad }).then(function () {
                this.load();
                this.$notify('Ad saved.');
            });
        },

        status: function(status) {

            var ads = this.getSelected();

            ads.forEach(function(ad) {
                ad.status = status;
            });

            this.resource.save({ id: 'bulk' }, { ads: ads }).then(function () {
                this.load();
                this.$notify('Ads saved.');
            });
        },

        remove: function() {

            this.resource.delete({ id: 'bulk' }, { ids: this.selected }).then(function () {
                this.load();
                this.$notify('Ads deleted.');
            });
        },

        toggleStatus: function (ad) {
            ad.status = ad.status === 2 ? 3 : 2;
            this.save(ad);
        },

        copy: function() {

            if (!this.selected.length) {
                return;
            }

            this.resource.save({ id: 'copy' }, { ids: this.selected }).then(function () {
                this.load();
                this.$notify('Ads copied.');
            });
        },

        load: function () {
            this.resource.query({ filter: this.config.filter, page: this.config.page }).then(function (res) {

                var data = res.data;

                this.$set('ads', data.ads);
                this.$set('pages', data.pages);
                this.$set('count', data.count);
                this.$set('selected', []);
            });
        },

        getSelected: function() {
            return this.ads.filter(function(ad) { return this.selected.indexOf(ad.id) !== -1; }, this);
        },

        getStatusText: function(ad) {
            return this.statuses[ad.status];
        }

    }

};

Vue.ready(module.exports);
