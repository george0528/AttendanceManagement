<template>
  <div>
    <v-sheet tile height="6vh" color="grey lighten-3" class="d-flex align-center">
     <v-btn outlined small class="ma-4" @click="setToday">
       今日
     </v-btn>
     <v-btn outlined small class="ma-4" @click="changeShowType">
       表示変更
     </v-btn>
      <v-btn icon @click="$refs.calendar.prev()">
        <v-icon>mdi-chevron-left</v-icon>
      </v-btn>
      <v-btn icon @click="$refs.calendar.next()">
        <v-icon>mdi-chevron-right</v-icon>
      </v-btn>
      <v-toolbar-title>{{ title }}</v-toolbar-title>
    </v-sheet>
    <v-sheet height="94vh">
      <v-calendar
        ref="calendar"
        v-model="value"
        event-start="start_time"
        event-end="end_time"
        :events="events"
        :event-color="getEventColor"
        @change="getEvents"
        locale="ja-jp"
        :type="type"
        :day-format="(timestamp) => new Date(timestamp.date).getDate()"
        :month-format="(timestamp) => (new Date(timestamp.date).getMonth() + 1) + ' /'"
      ></v-calendar>
    </v-sheet>
  </div>
</template>

<script>
import moment from 'moment';

export default {
  data: () => ({
    value: moment().format('yyyy-MM-DD'),  // 現在日時
    type_num: 1,
  }),
  props: ['events'],
  methods: {
    getEvents() {
      // const events = [
      //   {
      //     name: '会議',
      //     start: new Date('2021-12-03T01:00:00'), // 開始時刻
      //     end: new Date('2021-12-03T02:00:00'), // 終了時刻
      //     color: 'blue',
      //     timed: true, // 終日ならfalse
      //   },
      // ];
      // this.events = events;
    },
    getEventColor(event) {
      return event.color;
    },
    setToday() {
      this.value = moment().format('yyyy-MM-DD')
    },
    changeShowType() {
      this.type_num ++;
      if(this.type_num == 4) {
        this.type_num = 1
      }
    }
  },
  computed: {
    title() {
      return moment(this.value).format('yyyy年 M月');  // 表示用文字列を返す
    },
    type() {
      if(this.type_num == 1) {
        return 'day';
      }
      if(this.type_num == 2) {
        return 'week';
      }
      if(this.type_num == 3) {
        return 'month';
      }
    },
  }
};
</script>
