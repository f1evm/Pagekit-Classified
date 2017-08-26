window.AdUser = {

    el: '#aduser',

    data: function () {
        return {
            data: window.$data,
            ad: window.$data['ad'],
            config: window.$data['config']
        }
    },

    created: function () {

              this.resource = this.$resource('api/classified/ad{/id}');
    },

    ready: function () {
    //    this.tab = UIkit.tab(this.$els.tab, {connect: this.$els.content});
    },

    computed: {
        maxPhotos: function() {
          return this.config.photos.photos_per_ad;
        },

        nbPhotos: function() {
           return this.ad.photos.length;
       },

        listPhotos: function() {
          return this.ad.photos;
        },

        canAddPhotos: function() {
           return this.maxPhotos - this.nbPhotos;
        },


    },


    methods: {

        save: function () {
            var data = {ad: this.ad, id: this.ad.id};

            this.$broadcast('save', data);

            this.resource.save({id: this.ad.id}, data).then(function (res) {

                var data = res.data;

                if (!this.ad.id) {
                    window.history.replaceState({}, '', this.$url.route('classified/ad/edit', {id: data.ad.id}))
                }

                this.$set('ad', data.ad);

                this.$notify('Ad saved.');
                location.reload();


            }, function (res) {
                this.$notify(res.data, 'danger');
            });
        },

        select: function(num) {
              this.ad.ad_photo = num;
              this.save();
        },

        erase: function(num) {
          var adp = this.ad.ad_photo;
          UIkit.modal.confirm('Are you sure you want to delete this photo?', function(){
            if((num == adp) &&(num == this.nbPhotos-1)){
              adp--;
            } else if ((num < this.nbPhotos-1)&&(num < adp)){
              adp--;
            }


          });

          this.$http.post('/api/classified/ad/delPhoto',{num: num, id: this.ad.id, adp: adp}).then(function (res) {
            //resultat OK
            location.reload();
          }, function (res) {
            // resultat NOK
              this.$notify(res.data, 'danger');
          });



  //        fichier = '/storage/fevm/classified/' + this.ad.id + '/' + this.ad.photos[num];
  //        this.ad.photos.splice(num,1);
  //        this.save();

    //      UIkit.modal.alert('effacer le fichier : ' + fichier);

        }

    },



};


Vue.ready(window.AdUser);
