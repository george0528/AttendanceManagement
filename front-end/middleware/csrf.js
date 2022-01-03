export default function({$axios, $cookies}) {
  if(!$cookies.get('XSRF-TOKEN')) {
    $axios.$get('/sanctum/csrf-cookie')
  }
}