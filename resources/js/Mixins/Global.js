import * as marked from 'marked';

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
      return marked.parse(data);
    },
  },
}