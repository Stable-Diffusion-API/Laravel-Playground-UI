import './bootstrap';
import Alpine from 'alpinejs'

window.Alpine = Alpine;


/********** ALPINE FUNCTIONALITY **********/
document.addEventListener('alpine:init', () => {
    Alpine.store('toast', {
        type: '',
        message: '',
        show: false,

        update({ type, message, show }) {
            this.type = type;
            this.message = message;
            this.show = show;
        },

        close() {
            this.show = false;
        }
    });

    Alpine.store('plan_modal', {
        open: false,
        plan_name: 'basic',
        plan_id: 0,

        switch(plan_id, plan_name) {
            this.plan_name = plan_name;
            this.plan_id = plan_id;
            this.open = true;
        },

        close() {
            this.open = false;
        }
    });

    Alpine.store('viewApiKey', {
        open: false,
        id: '',
        name: '',
        key: '',

        actionClicked(id, name, key) {
            this.open = true;
            this.id = id;
            this.name = name;
            this.key = key;
        },

    });

    Alpine.store('editApiKey', {
        open: false,
        id: '',
        name: '',
        key: '',

        actionClicked(id, name, key) {
            this.open = true;
            this.id = id;
            this.name = name;
            this.key = key;
        },

    });

    Alpine.store('deleteApiKey', {
        open: false,
        id: '',
        name: '',
        key: '',

        actionClicked(id, name, key) {
            this.open = true;
            this.id = id;
            this.name = name;
            this.key = key;
        },

    });

    Alpine.store('confirmCancel', {
        open: false,

        openModal() {
            this.open = true;
        },

        close() {
            this.open = false;
        }

    });

    Alpine.store('uploadModal', {
        open: false,

        openModal() {
            this.open = true;
        },

        close() {
            this.open = false;
        }

    });

});

Alpine.start();
/********** END ALPINE FUNCTIONALITY **********/

/********** START TOAST FUNCTIONALITY **********/

window.popToast = function(type, message){
    Alpine.store('toast').update({ type, message, show: true });

    setTimeout(function(){
        document.getElementById('toast_bar').classList.remove('w-full');
        document.getElementById('toast_bar').classList.add('w-0');
    }, 1500);
    // After 4 seconds hide the toast
    setTimeout(function(){
        Alpine.store('toast').update({ type, message, show: false });

        setTimeout(function(){
            document.getElementById('toast_bar').classList.remove('w-0');
            document.getElementById('toast_bar').classList.add('w-full');
        }, 3000);
    }, 40000);
}

/********** END TOAST FUNCTIONALITY **********/

