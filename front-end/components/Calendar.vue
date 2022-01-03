<template>
  <div>
    <v-sheet tile height="6vh" color="grey lighten-3" class="d-flex align-center">
     <v-btn outlined small class="ma-4" @click="setToday">
       今日
     </v-btn>
     <v-btn outlined small class="ma-4" @click="changeShowType">
       表示変更
     </v-btn>
     <v-btn v-if="btn.flag" color="info" outlined small class="ma-4" @click="emitEvent">
      {{ btn.text }}
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
        :event-more="more_flag"
        @click:more="clickMore"
        @click:date="viewDay"
        @click:event="clickEvent"
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
    more_flag: true,
  }),
  props: ['events', 'btn'],
  methods: {
    getEvents() {

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
    },
    clickMore() {
      this.more_flag = !this.more_flag;
    },
    viewDay({ date }) {
      this.value = date;
      this.type_num = 1;
    },
    clickEvent(data) {
      this.$emit('clickEvent', data.event);
    },
    emitEvent() {
      this.$emit('btnClickEmit');
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
