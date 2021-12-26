// 自作関数

export default ({  }, inject) => {
  // カレンダーのデータを取得
  const getDates = async ($this, url, color = 'red', message) => {
    return await $this.$axios.get(url)
    .then(res => {
      var data = res.data;
      var events = data.map(event => {
        event.color = color;
        event.timed = true;
        return event;
      });
      return events;
    })
    .catch(e => {
      console.log(e);
      $this.$store.dispatch('flashMessage/showMessage', {
        message: message,
        type: 'error',
        status: true,
      })
      return [];
    });
  }

  inject('getDates', getDates);
}