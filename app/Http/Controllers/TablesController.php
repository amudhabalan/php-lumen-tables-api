<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Endroid\QrCode\QrCode;
use Throwable;

class TablesController extends Controller
{
    private $tableData = '{"Front":{"name":"Front","tables":{"75":{"name":"1","visible":1},"108":{"name":"2","visible":1},"102":{"name":"3","visible":1},"73":{"name":"4","visible":1},"79":{"name":"5","visible":1},"80":{"name":"6","visible":1},"109":{"name":"7","visible":1},"105":{"name":"7","visible":1},"110":{"name":"8","visible":1},"111":{"name":"9","visible":1},"112":{"name":"10","visible":1},"113":{"name":"11","visible":1},"115":{"name":"12","visible":1},"116":{"name":"13","visible":1},"63":{"name":"101","visible":1},"64":{"name":"102","visible":1},"65":{"name":"103","visible":1},"66":{"name":"104","visible":1},"90":{"name":"201","visible":1},"91":{"name":"202","visible":1},"92":{"name":"203","visible":1},"93":{"name":"204","visible":1},"86":{"name":"205","visible":1},"87":{"name":"206","visible":1},"88":{"name":"207","visible":1},"107":{"name":"501","visible":1},"83":{"name":"Invalid table name","visible":0},"77":{"name":"Invalid table name","visible":0},"82":{"name":"Invalid table name","visible":0},"84":{"name":"Invalid table name","visible":0},"106":{"name":"Invalid table name","visible":0},"62":{"name":"Invalid table name","visible":0},"85":{"name":"Invalid table name","visible":0},"117":{"name":"Space","visible":0}},"active_tables":26,"is_legacy":"false"},"Back":{"name":"Back","tables":{"95":{"name":"11","visible":1},"94":{"name":"12","visible":1},"97":{"name":"21","visible":1},"96":{"name":"22","visible":1},"99":{"name":"31","visible":1},"98":{"name":"32","visible":1},"101":{"name":"41","visible":1},"100":{"name":"42","visible":1},"42":{"name":"51","visible":1},"43":{"name":"52","visible":1},"44":{"name":"53","visible":1},"45":{"name":"54","visible":1},"46":{"name":"55","visible":1},"51":{"name":"61","visible":1},"52":{"name":"62","visible":1},"53":{"name":"63","visible":1},"54":{"name":"64","visible":1},"55":{"name":"65","visible":1},"35":{"name":"301","visible":1},"36":{"name":"302","visible":1},"37":{"name":"303","visible":1},"38":{"name":"304","visible":1},"39":{"name":"305","visible":1},"104":{"name":"306","visible":1},"60":{"name":"307","visible":1},"40":{"name":"Invalid table name","visible":0},"58":{"name":"Invalid table name","visible":0},"57":{"name":"Invalid table name","visible":0},"41":{"name":"Invalid table name","visible":0},"59":{"name":"Invalid table name","visible":0}},"active_tables":25,"is_legacy":"false"},"A":{"name":"A","tables":{"118":{"name":"1","visible":0}},"active_tables":0,"is_legacy":"false"}}';
    /**
     * Retrieve a list of visible tables.
     * Authentication for now is with a sample list of API Keys
     *
     * @return String
     *  A JSON string representing an array of visible tables
     */
    public function tables(Request $request)
    {
        $this->validate($request, [
            'apikey' => [
                'required',
                'uuid',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, [
                        'd4371401-219b-4676-8732-3dd2512694fe',
                        '46610df7-99d1-4657-bc1b-67f0341bddb2',
                        '5fe2d632-3e25-4e78-9950-85b01b88092a'
                    ])) {
                        $fail($attribute.' is invalid.');
                    }
                }, 
            ]
        ]);
        try{
            return response()->json($this->getVisibleTables(json_decode($this->tableData)))->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Headers', '*');    
        }catch(Throwable $e){
            var_dump($e->getMessage());
            exit;
            return response()->json(['error' => 'Something went wrong'], 500);
        }
        
    }

    /**
     * Returns an array of visible tables from the given restaurant data
     * 
     * @param Object $tableData 
     * @return array
     */
    private function getVisibleTables($tableData) {
        $visibleTables = [];
        $host = $_SERVER['HTTP_HOST'];
        $protocol=$_SERVER['PROTOCOL'] = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
        foreach($tableData as $room) {
            foreach ($room->tables as $tableId => $table) {
                if ($table->visible === 1) {
                    $visibleTables[] = ['room' => $room->name, 'tableId' => $tableId, 'tableName' => $table->name, 'qrCode' => "{$protocol}://{$host}/qrcode/{$tableId}"];
                }
            }
        }
        return $visibleTables;
    }

    /**
     * Return QRCode for the given table Id
     *
     * @param int $table_id
     * @return String
     */
    public function qrcode($table_id)
    {
        try {
            $qrCode = new QrCode('https://dev.hungryhungry.com/oceana2/menu?locationID=1995257&amp;tableID='.$table_id);
            return (new Response($qrCode->writeString(), 200))
                  ->header('Content-Type', $qrCode->getContentType())->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Headers', '*');  ;  ;
        } catch (Throwable $e) {
            return response()->json(['error' => $e->message], 500);
        }
        
    }
}
