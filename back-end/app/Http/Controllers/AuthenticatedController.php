<?php

namespace App\Http\Controllers;

use App\Services\AuthenticatedService;
use Illuminate\Http\Request;

class AuthenticatedController extends Controller
{
  private $service;

  public function __construct(AuthenticatedService $service) {
    $this->service = $service;
  }

  public function getSchedule()
  {
    return $this->service->getSchedule();
  }
  public function getHistory()
  {
    return $this->service->getHistory();
  }
}
