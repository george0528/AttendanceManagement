// 自作関数

export default ({  }, inject) => {
  const getDates = async ($this, url, color = 'red') => {
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
      this.$store.dispatch('flashMessage/showMessage', {
        message: '就業履歴の取得に失敗しました',
        type: 'error',
        status: true,
      })
      return [];
    });
  }

  inject('getDates', getDates);
}