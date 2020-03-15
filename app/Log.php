<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';

    protected $fillable = [
        'log_level',
        'error_code',
        'request_id',
        'request_type',
        'message',
        'controller',
        'method',
        'loc',
        'file',
        'url',
        'input_data',
        'old_data',
        'new_data',
        'trace',
        'is_resolved',
        'is_concerned',
        'created_by'
    ];

    protected $hidden = [
        'id'
    ];

    /**
     * Constants
     * Follow log levels from Monolog
     */
    const LEVEL_DEBUG       = 'DEBUG';          // 100
    const LEVEL_INFO        = 'INFO';           // 200
    const LEVEL_NOTICE      = 'NOTICE';         // 250
    const LEVEL_WARNING     = 'WARNING';        // 300
    const LEVEL_ERROR       = 'ERROR';          // 400
    const LEVEL_CRITICAL    = 'CRITICAL';       // 500  // For unexpected exception
    const LEVEL_ALERT       = 'ALERT';          // 550
    const LEVEL_EMERGENCY   = 'EMERGENCY';      // 600

    /*
     * Helpers
     */
    protected static function write($logLevel, \Exception $exception) {
        try {
            $message = $exception->getMessage();
            $loc = $exception->getLine();
            $file = $exception->getFile();
            $trace = $exception->getTraceAsString();

            Log::create([
                'log_level' => $logLevel,
                'message'   => $message,
                'loc'       => $loc,
                'file'      => $file,
                'trace'     => $trace
            ]);
        }
        catch(\Exception $exception) {
            dd($exception->getMessage());
        }
    }
    public static function error(\Exception $exception) {
        self::write(self::LEVEL_ERROR, $exception);
    }

    public static function critical(\Exception $exception) {
        self::write(self::LEVEL_CRITICAL, $exception);
    }
}
