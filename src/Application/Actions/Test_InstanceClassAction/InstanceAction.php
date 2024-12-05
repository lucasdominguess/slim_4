<?php
namespace App\Application\Actions\Test_InstanceClassAction;


use App\classes\Helpers;
use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface;

class InstanceAction extends Action
{
    use Helpers;
  public function action(): ResponseInterface
  {
    // $this->logger->info('instanceAction');
    // $this->logger->warning('instanceAction');
    // $this->logger->pushProcessor();

    print_r($_ENV);

    return $this->respondWithData([
      'message' => $_ENV]);
  }
}