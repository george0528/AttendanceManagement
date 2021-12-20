export default function({$axios}) {
  $axios.get('/sanctum/csrf-cookie');
}