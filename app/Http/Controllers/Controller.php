<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Raju\Streamer\Helpers\VideoStream;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function getArchivos(){
        $thefolder = "Archivos/";
        $ar = [];
        $dir = [];
        if ($handler = opendir($thefolder)) {
            while (false !== ($file = readdir($handler))) {
                $ruta_completa = $thefolder.$file;
                if(is_dir($ruta_completa)){
                    array_push($dir,[$file,date("d-m-Y h:i:s A", filectime($ruta_completa))]);
                }else{
                    $tam =  filesize($ruta_completa);
                    if($tam > 1024){
                        $tam = $tam/1024;
                        if($tam > 1024){
                            $tam = $tam/1024;
                            if($tam > 1024){
                                $tam = $tam/1024;
                                $tam = round($tam)." GB";
                            }else{
                                $tam = round($tam)." MB";
                            }
                        }else{
                            $tam = round($tam)." KB";
                        }
                    }else{
                        $tam = round($tam)." bites";
                    }

                    array_push($ar,[$file,date("d-m-Y h:i:s A", filectime($ruta_completa)),$tam]);
                }
            }
            closedir($handler);
        }
        return Response::json(["archivos"=>$ar,'carpetas'=>$dir]);
    }

    function crearCarpeta(Request $request) {
        if(!mkdir("Archivos/$request->carpeta/$request->nombre", 0777, true)) {
            die('Fallo al crear las carpetas...');
        }
        return Response::json(["code"=>200]);
    }

    function openFolder(Request $request){
        $thefolder = "Archivos".$request->carpeta."/";
        $ar = [];
        $dir = [];
        if ($handler = opendir($thefolder)) {
            while (false !== ($file = readdir($handler))) {
                $ruta_completa = $thefolder.$file;
                if(is_dir($ruta_completa) || "".$file == "." || "".$file == ".."){
                    array_push($dir,[$file,date("d-m-Y h:i:s A", filectime($ruta_completa))]);
                }else{
                    $tam =  filesize($ruta_completa);
                    if($tam > 1024){
                        $tam = $tam/1024;
                        if($tam > 1024){
                            $tam = $tam/1024;
                            if($tam > 1024){
                                $tam = $tam/1024;
                                $tam = round($tam)." GB";
                            }else{
                                $tam = round($tam)." MB";
                            }
                        }else{
                            $tam = round($tam)." KB";
                        }
                    }else{
                        $tam = round($tam)." bites";
                    }

                    array_push($ar,[$file,date("d-m-Y h:i:s A", filectime($ruta_completa)),$tam]);
                }
            }
            closedir($handler);
        }
        return Response::json(["archivos"=>$ar,'carpetas'=>$dir]);
    }

    function upload(Request  $request){
        $thefolder = "Archivos$request->carpeta/";
        $ar = [];
        for($i = 0; $i<$request->numarch; $i++){
            $file = $request->file('file'.$i);
            $nombre = $file->getClientOriginalName();
            \Storage::disk('local')->put("/$thefolder".$nombre,  \File::get($file));
            array_push($ar,[$nombre,date("d-m-Y h:i:s A")]);
        }
        Response::json(["code"=>200]);
    }

    function download(Request $request){
        $carpeta = $request->carpeta;
        $carpeta = str_replace(".","/",$carpeta);
        $file = "Archivos$carpeta/$request->archivo";
        $contenttype = mime_content_type( $file );
        $headers = ["Content-Type: application/$contenttype"];

        return Response::download($file,'',$headers);
    }

    function eliminar(Request $request){
        try {
            $carpeta = $request->carpeta;
            #$carpeta = str_replace(".","/",$carpeta);
            $file = "Archivos$carpeta/$request->nombre";
            Storage::disk('local')->delete($file);
            return Response::json(["code"=>200]);
        }catch (Exception $e){
            return Response::json(["code"=>300]);
        }
    }

    function eliminaCarpeta(Request $request){
        try {
            $carpeta = "Archivos".$request->carpeta."/$request->nombre";
            Storage::disk('local')->deleteDirectory($carpeta);
            return Response::json(["code"=>200]);
        }catch (Exception $e){
            return Response::json(["code"=>300]);
        }
    }

    function videoStream($file) {
        $videosDir      = public_path();
        $filePath = $videosDir."/videos/".$file;

        if (file_exists($filePath = $videosDir."/videos/".$file)) {
            $stream = new VideoStream($filePath);
            return response()->stream(function() use ($stream) {
                $stream->start();
            });
        }
        return response("File doesn't exists", 404);
    }
}
