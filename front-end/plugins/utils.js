// 自作関数

export default ({  }, inject) => {
  // カレンダーのデータを取得
  const getDates = async ($this, url, color = 'blue', message) => {
    return await $this.$axios.get(url)
    .then(res => {
      var data = res.data;
      var events = data.map(event => {
        event.color = 'blue';
        event.timed = true;

        if(event.absence_request == null) {
          return event;
        }

        // 欠勤申請済みで未承諾の場合
        if(event.absence_request.request_check == 0) {
          event.color = 'grey';
          event.name = '欠勤申請が未承諾';
          event.is_absence_requested = true;
          return event;
        } 

        // 欠勤申請が承諾されていたら
        event.color = 'red';
        event.name = '欠勤申請承諾済み';
        event.is_absence_requested = true;
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