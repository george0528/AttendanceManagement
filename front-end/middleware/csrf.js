export default function({$axios, $cookies}) {
  if(!$cookies.get('XSRF-TOKEN') || !$cookies.get('backend_cookie')) {
    $axios.$get('/sanctum/csrf-cookie')
  }
}