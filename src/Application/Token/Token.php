<?php
namespace App\Application\token;

use DateTime;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\classes\Helpers;
use App\classes\CreateLogger;
use Firebase\JWT\ExpiredException;
use Psr\Container\ContainerInterface;
use Firebase\JWT\SignatureInvalidException;
use Psr\Http\Message\ServerRequestInterface as request;

class Token
{   
    use Helpers;
    protected Request $request;

    protected static ?ContainerInterface $container = null;  
    private function __construct
    (
        protected CreateLogger $log,
        )
    {
      
        
    }
    /**
     * Cria um Token de usuario 
     * @param $email Adiciona email do usuario ao payload
     * @param string $time Adiciona o tempo limite para expirar o token 
     */
    public function generateToken($user): string
    {         
        global $env;
        $time =  $env['exp_token'] ?? '60 minutes';
        $key = $env['secretkey'];
        
        $datenow = new DateTime('now', $GLOBALS['TZ']); 
        $datenow->add(date_interval_create_from_date_string($time)); // adc a hora atual o tempo em string "10 minutes"

        $user;
        
        $payload = [
            'exp' => $datenow->getTimestamp(),
            'email' => $user[0],
            'name' => $user[1],
            'login' => $user[2],
        ];  
        try {
            $this->TokenConst($payload);
            $jwt = JWT::encode($payload, $key, 'HS256');
        } catch (\Throwable $th) {

            $this->log->loggerCSV("Token_generate_fail",'tentativa de gerar token falhou'.$th->getMessage(),'warning',$_SERVER['REMOTE_ADDR']);
            throw new Exception("Não foi possivel gerar token");
        }   
        //gerando constant com dados do usuario 
        // setcookie("Authorization", $jwt, COOKIE_OPTIONS);
        return $jwt;
    }

    public function decodedToken($cookie)
    {
        global $env;
        $key = $env['secretkey'];

        if (!$cookie) {
            $this->log->loggerCSV("token_decoded_fail", 'tentativa de acesso com Cookie/Token Inexistente ou Invalido', 'warning', $_SERVER['REMOTE_ADDR']);
            throw new Exception("Cookie/Token Inexistente ou Invalido");
        }

        try {
            $decoded_array = (array) JWT::decode($cookie, new Key($key, 'HS256'));
            $this->tokenConst($decoded_array);
            return $decoded_array;
        } catch (ExpiredException|SignatureInvalidException $ex) {
            $message = $ex instanceof ExpiredException ? 'Token Expirado' : 'Token Alterado ou Invalido';
            $this->log->loggerCSV("token_decoded_fail", "tentativa de acesso com $message", 'warning', $_SERVER['REMOTE_ADDR']);
            throw new Exception("Acesso nao permitido: $message", 403);
            
        } catch (\Throwable $th) {
            $this->log->loggerCSV("token_decoded_fail", $th->getMessage(), 'warning', $_SERVER['REMOTE_ADDR']);
            throw new Exception("Erro ao processar o Token", 500);
        }
    }
    public function tokenConst(array $payload)
    {   
       try {
           define ('USER_NAME',$payload['name']);
           define ('USER_EMAIL',$payload['email']);  
           define ('USER_LOGIN',$payload['login']);  
       } catch (\Throwable $th) {
            $this->log->loggerCSV('token_constants_fail',$th->getMessage());
            throw new Exception('Erro ao criar constantes de sessao do usuario');
       }
    }

    public function destructHeaderToken ()
    {   
        
        global $env;
        $this->request->withHeader(
            'Set-Cookie',
            'Authorization=' .''. '; ' .
            'Max-Age=' .-1 . '; ' .
            'Path=' .$env['path'].'; '
        );
    }

    public static function setContainer(ContainerInterface $container): void
    {
        static::$container = $container;
    }

    public static function create(): Token
    {
        return new self(
         static::$container->get(CreateLogger::class),
        );
    }
}
