import * as marked from 'marked';
import { copyText } from 'vue3-clipboard';
import { ref } from 'vue';

export default {
  methods: {
    hasAnyRole: function (roles) {
      return this.checkIfValueExists(roles, 'roles')
    },
    hasAnyPermission: function (permissions) {
      return this.checkIfValueExists(permissions, 'permissions')
    },
    checkIfValueExists(queryArray, type){
      let allValues = Array.from(this.$page.props.user[type])
      return queryArray.some(r=> allValues.indexOf(r) >= 0);
    },
    formatDate(timestamp) {
        const date = new Date(timestamp);
        return new Intl.DateTimeFormat('default', {dateStyle: 'long'}).format(date);
    },
    formatDateTime(timestamp) {
      const date = new Date(timestamp);
      return new Intl.DateTimeFormat('en', { dateStyle: 'full', timeStyle: 'short' }).format(date);
    },
    md(data) {
      return data ? marked.parse(data) : "";
    },
    copyToClipboard(text, id){
      document.getElementById(id).select();
      copyText(text, undefined, (error, event) => {
        if (error) {
          console.log(error)
        } else {
          // console.log(event)
        }
      })
    }
  },
}