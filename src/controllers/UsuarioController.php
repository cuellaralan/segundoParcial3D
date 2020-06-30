<?php

namespace App\controllers;
//class
use App\models\Usuario;
use App\models\Mascota;
use App\models\Turno;
use App\models\Tipo_Mascota;
//slim
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//JWT
use \Firebase\JWT\JWT;
//capsula
// use Config\Capsula;

//obtengo headers
$headers = getallheaders();
//verifico token
$token = $headers['token'] ?? '';
//JWT

class UsuarioController{
    public function getAll(Request $request, Response $response){
        $users = Usuario::all();
        $resp = json_encode($users);
        $response->getBody()->write($resp);
        return $response;
    }

    // public function addTime(Request $request, Response $response){
    //     Capsula::schema()->table('mascotas', function ($table) {
    //     $table->timestamps();
    //     $table->softDeletes();
    //     });
    //     // if (Schema::hasColumn('usuarios', 'updated_at'))
    //     // {
    //     //     $rta = array("rta" => "existe");
    //     // }
    //     // else {
    //     //     $rta = array("rta" => "NO existe");
    //     // }
    //     //modifico tabla, creando los campos de TIME created_at y updated_at
    //         $response->getBody()->write("todoOK");
    //         return $response;
    // }

    public function logup(Request $request, Response $response){
        /* 
        Registrar en una tabla de usuario, email, tipo de usuario y password encriptado.
        El tipo de usuario puede ser cliente, veterinario o admin. Validar que el mail no esté registrado
        previamente.*/
        //1 admin
        //2 veterinario
        //3 cliente
        //obtengo parametros
        $email = $request->getParsedBody()['email'] ?? '';
        $tipo = $request->getParsedBody()['tipo'] ?? '';
        $clave = $request->getParsedBody()['clave'] ?? '';
        $nombre = $request->getParsedBody()['usuario'] ?? '';

        //respuesta
        $rta = array('rta' => 'error en operación', 'status' => 'unsucces');
        $exist = false;
        if ($email != '' && $clave != '' && $tipo != '' && $nombre != '') {
            if ($tipo == 'veterinario' || $tipo == 'cliente' || $tipo == 'admin') {
                $user = new Usuario;
                $user->email = $email;
                switch ($tipo) {
                    case 'admin':
                        $user->tipo = 1;
                    break;
                    case 'veterinario':
                        $user->tipo = 2;
                    break;
                    case 'cliente':
                        $user->tipo = 3;
                        break;
                    default:
                        break;
                }
                $user->clave = $clave;
                $user->usuario = $nombre;
                $usuarios = Usuario::all();
                foreach ($usuarios as $key => $value) {
                    // echo(json_encode($value));
                    if ($value->email == $email) {
                        $exist = true;
                        $rta['rta'] = 'usuario existente';
                        break;
                    }
                }
                if(!$exist){
                    $rta['rta'] = $user->save();
                    $rta['status'] = 'succes';
                }
            }
            else{
                $rta['rta'] = 'tipo de usuario invalido';
            }
        }
        else {
            $rta['rta'] = 'error en los parametros';
        }
        //cambiar respuesta a array y json_encode , y agregar softDelete
        $response->getBody()->write(json_encode($rta));
        return $response;
        
    }

    public function login(Request $request, Response $response){
         //obtengo parametros
         $email = $request->getParsedBody()['email'] ?? '';
         $clave = $request->getParsedBody()['clave'] ?? '';
     
         //respuestas
         $rta = array('status' => 'succes');
         $exist = false;
         $keyjwt = "example_key";
         if ($email != '' && $clave != '' ) {
                 $usuarios = Usuario::all();
                 foreach ($usuarios as $key => $value) {
                     if ($value->email == $email && $value->clave == $clave) {
                         $exist = true;
                         break;           
                     }
                 }
                 if($exist){
                    $payload = array(
                        "iss" => "http://example.org",
                        "aud" => "http://example.com",
                        "iat" => 1356999524,
                        "nbf" => 1357000000,
                        "email" => $value->nombre,
                        "id" => $value->id,
                        "tipo" => $value->tipo);
                    $jwt = JWT::encode($payload, $keyjwt);
                    $rta['data'] = $jwt;
                 }
                 else {
                    $rta['data'] = 'Usuario o Clave invalidos';
                    $rta['status'] = 'unsucces';
                 }
             }
             else {
                 $rta['rta'] = 'error en los parametros';
                 $rta['status'] = 'unsucces';
         }
         //cambiar respuesta a array y json_encode , y agregar softDelete
         $response->getBody()->write(json_encode($rta));
         return $response;
    }

    public function addType(Request $request, Response $response){
        //respuesta
        $rta = array('data' => 'error en operación', 'status' => 'unsucces');
        $exist = false;
        //JWT
        //obtengo headers
        $headers = getallheaders();
        //verifico token JWT
        $token = $headers['token'] ?? '';
        $keyjwt = "example_key";
        $verifica = false;
        if ($token == '') {
            // $response = new Lresponse();
            $$rta['data'] = 'error , token incorrecto';
        }
        else{
            try {
                //code...
                $decoded = JWT::decode($token, $keyjwt, array('HS256'));
                $verifica = true;
                // print_r($decoded);
            } catch (\Throwable $th) {
                //throw $th;
                $rta['data'] = $th;
            }
        }
        if($verifica){
            if($decoded->tipo == 'admin'){
                $tipo_mascota = $request->getParsedBody()['tipo'] ?? '';
                $types = Tipo_Mascota::all();
                foreach ($types as $key => $value) {
                    if($value->tipo == $tipo_mascota){
                        $rta['data'] = 'tipo de mascota existente';
                        $exist = true;
                    }
                }
                if(!$exist){
                    $typePet = new Tipo_Mascota;
                    $typePet->tipo = $tipo_mascota;
                    $rta['data'] = $typePet->save();
                    $rta['status'] = 'succes';
                }
                // $rta['data'] = $types;
            }
            else{
                $rta['data'] = 'usuario NO autorizado';
            }
        }
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function addPet(Request $request, Response $response){
        //obtengo parametros
        $nombre = $request->getParsedBody()['nombre'] ?? '';
        $edad = $request->getParsedBody()['nacimiento'] ?? '';
        $tipo = $request->getParsedBody()['tipo'] ?? '';
        //respuesta
        $rta = array('rta' => 'error en operación', 'status' => 'unsucces');
        $exist = false;
        //JWT
        //obtengo headers
        $headers = getallheaders();
        //verifico token JWT
        $token = $headers['token'] ?? '';
        $keyjwt = "example_key";
        $verifica = false;
        if ($token == '') {
            // $response = new Lresponse();
            $$rta['data'] = 'error , token incorrecto';
        }
        else{
            try {
                //code...
                $decoded = JWT::decode($token, $keyjwt, array('HS256'));
                $verifica = true;
                // print_r($decoded);
            } catch (\Throwable $th) {
                //throw $th;
                $rta['data'] = $th;
            }
        }
        if ($nombre != '' && $edad != '' && $tipo != '' && $verifica) {
            if ($decoded->tipo == 3) {
                $rta['rta'] = 'es cliente ok';
                $pet = new Mascota;
                $pet->nombre = $nombre;
                $pet->fecha_nacimiento = $edad;
                //tipo_mascota_id
                $tmascota = Tipo_Mascota::select('id')
                ->where('tipo', $tipo)
                ->get();
                if(!empty($tmascota))
                {
                    $pet->tipo_mascota_id = $tmascota[0]['id'];
                }
                //cliente_id
                $pet->cliente_id = $decoded->tipo;
                $mascotas = Mascota::all();
                foreach ($mascotas as $key => $value) {
                    // echo(json_encode($value));
                    if ($value->nombre == $nombre && $decoded->id == $value->id_cliente) {
                        $exist = true;
                        $rta['rta'] = 'mascota existente para el cliente';
                    break;
                    }
                }
                if(!isset($pet->tipo_mascota_id)){
                    $exist = true;
                    $rta['rta'] = $pet->tipo_mascota_id;
                }
                if(!$exist){
                    $rta['rta'] = $pet->save();
                    $rta['status'] = 'succes';
                }
            }   
            else{
                $rta['rta'] = 'tipo de usuario INVALIDO';
            }
        }
        else {
            $rta['rta'] = 'error en los parametros';
        }
        //cambiar respuesta a array y json_encode , y agregar softDelete
        $response->getBody()->write(json_encode($rta));
        return $response;
        
    }

     
    public function addTurn(Request $request, Response $response){
        //obtengo parametros
        $fecha = $request->getParsedBody()['fecha'] ?? '';
        $hora = $request->getParsedBody()['hora'] ?? '';
        //respuesta
        $rta = array('status' => 'unsucces', 'data' => 'error en operación');
        $exist = false;
        $disponibles = 2;
        $VetTurno= 0;
        //JWT
        //obtengo headers
        $headers = getallheaders();
        //verifico token JWT
        $token = $headers['token'] ?? '';
        $keyjwt = "example_key";
        $verifica = false;
        if ($token == '') {
            // $response = new Lresponse();
            $rta['data'] = 'error , token incorrecto';
        }
        else{
            try {
                //code...
                $decoded = JWT::decode($token, $keyjwt, array('HS256'));
                $verifica = true;
                // print_r($decoded);
            } catch (\Throwable $th) {
                //throw $th;
                $rta['data'] = $th;
            }
        }
        if ($fecha == '' || $hora == '') {
            $rta['data'] = 'error en los parametros ' . $fecha . $hora;
            $verifica = false;
        }
        else{
            $explode = explode(':',$hora);
            $hours = $explode[0];
            $mins = $explode[1];
            // var_dump($explode);
            if( ($hours > '19' || $hours < '09') || !($mins = '00' || $mins = '30') ){
                $rta['data'] = 'hora de turno incorrecta: ' . $hours . $mins;
                $verifica = false;
            }
        }
        if($verifica){
            if ($decoded->tipo != 3) {
                $rta['data'] = 'tipo de usuario invalido';
                $verifica = false;
            }
        }
        if($verifica)
        {
            $turn = new Turno;
            $turn->fecha = $fecha;
            $turn->hora = $hora;
            //id de mascota del cliente
            $pet = Mascota::Where('cliente_id','=',$decoded->id)->take(1)->get();
            // $rta['data'] = $pet;
            if(!empty($pet)){
                $turn->id_mascota = $pet[0]['id'];
                $turnos = Turno::all();
                foreach ($turnos as $key => $value) {
                    // echo(json_encode($value));
                    $horaT = explode(':', $value->hora);
                    $hourT = $horaT[0];
                    $minsT = $horaT[1];
                    if ($value->fecha == $turn->fecha && $hourT == $hours && $minsT == $mins) {
                        if($value->id_mascota == $turn->id_mascota){
                            $verifica = false;
                            $rta['data'] = 'turno ya asignado';
                        break;
                        }
                        $exist = true;
                        $VetTurno = $value->id_veterinario;
                        $disponibles = $disponibles -1 ;
                    }
                }
            }
            else
            {
                $verifica = false;
                $rta['data'] = 'mascota no encontrada';
            }
            if($verifica){
                if(!$exist){
                    // $veter = Usuario::WhereRaw('tipo = ? ', [2])->take(1)->get();
                    $veter = Usuario::select('id')
                            ->where('tipo', 2)
                            ->get();
                    if(!empty($veter))
                        $turn->id_veterinario = $veter[0]['id'];
                        $rta['data'] = $turn->save();
                        $rta['status'] = 'succes';
                    }
                    else{
                        $rta['data'] = $veter;
                    }
                }
                else{
                    switch ($disponibles) {
                        case 1:
                            $veter = Usuario::select('id')
                            ->where('tipo','=' ,2)
                            ->where('id','!=' ,$VetTurno)
                            ->get();
                            if(!isset($veter)){
                                $turn->id_veterinario = $veter[0]['id'];
                                $rta['data'] = $turn->save();
                                $rta['status'] = 'succes';
                            }
                            else{
                                $rta['data'] = 'no hay veterinarios disponibles';
                            }
                            break;
                        case 0:
                            $rta['data'] = 'No hay veterinarios disponibles para el turno solicitado';
                            break;
                        default:
                            break;
                    }
                }
            }
        
        // cambiar respuesta a array y json_encode , y agregar softDelete

        $response->getBody()->write(json_encode($rta));
        return $response;
        
    }

    public function getTurns(Request $request, Response $response, $args){
        $qryParams = $request->getQueryParams();
        $rta = array('status' => 'unsucces', 'data' => 'error en operación');
        $rta['data'] = $qryParams;
        $fechaActual = date('Y-m-d');
        //5-Los veterinarios deben poder ver los turnos que tienen
        //asignados en el día con el nombre de la mascota, la hora, y la fecha del último turno anterior
        //de la mascota.
        $id_usuario = $qryParams['id_usuario'] ?? '';
        //obtengo tipo de cliente con el id
        $tipoCliente = Usuario::select('tipo')
            ->where('id',$id_usuario)
            ->get();
        $turnosVet = 0;
        switch ($tipoCliente[0]['tipo']) {
            case 'veterinario':
                //turnos del día para el veterinario
                $turnosVet = Turno::select('turnos.id_mascota', 'mascotas.nombre', 'turnos.fecha' , 'turnos.hora')
                ->join('mascotas', 'turnos.id_mascota','mascotas.id')
                ->where('turnos.id', $id_usuario)
                ->get();
                $turnosHoy = array();
                foreach ($turnosVet as $key => $value) {
                    // $value['turno-anterior'] = Turno::select('nombre from mascotas where id_cliente = 1')->get();
                    $turnosM = Turno::select('fecha')
                    ->where('id_mascota', $value->id_mascota)
                    ->where('fecha', '<', $value->fecha)
                    ->orderBy('fecha', 'desc')
                    ->get();
                    $value['turno-anterior'] = $turnosM[0];
                }
                break;
            case 'cliente':
                $turnosVet = Turno::select('id_mascota', 'fecha' , 'hora')
                ->where('id', $id_usuario)
                ->get();
                break;
            default:
                $rta['data'] = 'no se encontro usuario';
                $turnosVet = 1;
                break;
        }
        $response->getBody()->write(json_encode($turnosVet));
        return $response;
    }
}