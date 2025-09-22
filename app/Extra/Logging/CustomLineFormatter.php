<?php

namespace App\Extra\Logging;


use Monolog\Formatter\NormalizerFormatter;
use Monolog\LogRecord;
use Monolog\Utils;

class CustomLineFormatter extends NormalizerFormatter
{
    public const SIMPLE_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
    public const CODE_LINES_SPREAD = 8;

    /** @var string */
    protected $format;
    /** @var bool */
    protected $allowInlineLineBreaks;
    /** @var bool */
    protected $ignoreEmptyContextAndExtra;
    /** @var bool */
    protected $includeStacktraces;

    /**
     * @param string|null $format                     The format of the message
     * @param string|null $dateFormat                 The format of the timestamp: one supported by DateTime::format
     * @param bool        $allowInlineLineBreaks      Whether to allow inline line breaks in log entries
     * @param bool        $ignoreEmptyContextAndExtra
     */
    public function __construct(?string $format = null, ?string $dateFormat = null, bool $allowInlineLineBreaks = false, bool $ignoreEmptyContextAndExtra = false, bool $includeStacktraces = false)
    {
        $this->format = $format === null ? static::SIMPLE_FORMAT : $format;
        $this->allowInlineLineBreaks = $allowInlineLineBreaks;
        $this->ignoreEmptyContextAndExtra = $ignoreEmptyContextAndExtra;
        $this->includeStacktraces($includeStacktraces);
        parent::__construct($dateFormat);
    }

    public function includeStacktraces(bool $include = true): self
    {
        $this->includeStacktraces = $include;
        if ($this->includeStacktraces) {
            $this->allowInlineLineBreaks = true;
        }

        return $this;
    }

    public function allowInlineLineBreaks(bool $allow = true): self
    {
        $this->allowInlineLineBreaks = $allow;

        return $this;
    }

    public function ignoreEmptyContextAndExtra(bool $ignore = true): self
    {
        $this->ignoreEmptyContextAndExtra = $ignore;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function format( LogRecord $record ): string
    {
        $vars = parent::format($record);

        $output = $this->format;

        foreach ($vars['extra'] as $var => $val) {
            if (false !== strpos($output, '%extra.'.$var.'%')) {
                $output = str_replace('%extra.'.$var.'%', $this->stringify($val), $output);
                unset($vars['extra'][$var]);
            }
        }

        foreach ($vars['context'] as $var => $val) {
            if (false !== strpos($output, '%context.'.$var.'%')) {
                $output = str_replace('%context.'.$var.'%', $this->stringify($val), $output);
                unset($vars['context'][$var]);
            }
        }

        if ($this->ignoreEmptyContextAndExtra) {
            if (empty($vars['context'])) {
                unset($vars['context']);
                $output = str_replace('%context%', '', $output);
            }

            if (empty($vars['extra'])) {
                unset($vars['extra']);
                $output = str_replace('%extra%', '', $output);
            }
        }

        foreach ($vars as $var => $val) {
            if (false !== strpos($output, '%'.$var.'%')) {
                $output = str_replace('%'.$var.'%', $this->stringify($val), $output);
            }
        }

        // remove leftover %extra.xxx% and %context.xxx% if any
        if (false !== strpos($output, '%')) {
            $output = preg_replace('/%(?:extra|context)\..+?%/', '', $output);
            if (null === $output) {
                $pcreErrorCode = preg_last_error();
                throw new \RuntimeException('Failed to run preg_replace: ' . $pcreErrorCode . ' / ' . Utils::pcreLastErrorMessage($pcreErrorCode));
            }
        }

        return $output;
    }

    public function formatBatch(array $records): string
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }

    /**
     * @param mixed $value
     */
    public function stringify($value): string
    {
        return $this->replaceNewlines($this->convertToString($value));
    }

    protected function normalizeException(\Throwable $e, int $depth = 0): string
    {
        $str = $this->formatException($e);

        // Как правило это не нужная информация, поэтому пока закомментировано
        //        if ($previous = $e->getPrevious()) {
        //            do {
        //                $str .= "\n[previous exception] " . $this->formatException($previous);
        //            } while ($previous = $previous->getPrevious());
        //        }

        return $str;
    }

    /**
     * @param mixed $data
     */
    protected function convertToString($data): string
    {
        if (null === $data || is_bool($data)) {
            return var_export($data, true);
        }

        if (is_scalar($data)) {
            return (string) $data;
        }

        return $this->toJson($data, true);
    }

    protected function replaceNewlines(string $str): string
    {
        if ($this->allowInlineLineBreaks) {
            if (0 === strpos($str, '{')) {
                return str_replace(array('\r', '\n'), array("\r", "\n"), $str);
            }

            return $str;
        }

        return str_replace(["\r\n", "\r", "\n"], ' ', $str);
    }

    private function formatException(\Throwable $e): string
    {

        $data = [
            'object' => Utils::getClass($e),
            'e_code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'errorId' => dechex(time() - 1668794700 . rand(10000,99999))
        ];

        if ($e instanceof \SoapFault){

            if (isset($e->faultcode))
                $data['faultcode'] = $e->faultcode;


            if (isset($e->faultactor))
                $data['faultactor'] = $e->faultactor;

            if (isset($e->detail))
                $data['detail'] = $e->detail;

        }

        if ($this->includeStacktraces) {

            $trace = [];
            foreach( $e->getTrace() as $t ){

                if( !isset($t['file']) )
                    continue;

                $t_msg = '';

                if( isset($t['class']) )
                    $t_msg .= $t['class'];

                if( isset($t['type']) )
                    $t_msg .= $t['type'];

                if( isset($t['function']) )
                    $t_msg .= $t['function'];

                $t_msg .= '(';

                $args = [];
                foreach( $t['args'] ?? [] as $arg ){

                    if( is_object($arg) ){

                        $args[] = Utils::getClass($arg);

                    }elseif( is_array($arg) ){

                        $args[] = 'Array';

                    }elseif( is_string($arg) ){

                        $args[] = "'".$arg."'";

                    }else{

                        $args[] = $arg;

                    }

                }
                $t_msg .= implode(', ', $args).')';

                $trace[] = [
                    'file' => $t['file'],
                    'line' => $t['line'],
                    'msg' => $t_msg
                ];

            }

            $data['trace'] = $trace;

        }

        $code_line = 0;
        $code_first_line = 0;
        if( file_exists($e->getFile()) ){

            $file_code = file_get_contents( $e->getFile());
            $file_code = str_replace('\n', '//n//', $file_code);

            $spread = self::CODE_LINES_SPREAD; //Сколько строк кода показываем до и после нужно строки

            $start_line = $e->getLine() - $spread;
            if( $start_line < 0 )
                $start_line = 0;

            $code_first_line = $start_line;

            $end_line = $e->getLine() + $spread;

            $first_line = 0;
            $code = '';

            foreach( explode("\n", $file_code) as $key => $line ){

                $line_number = $key + 1;

                if( $line_number < $start_line || $line_number > $end_line )
                    continue;

                if( $e->getLine() == $line_number )
                    $code_line = $line_number;

                if( $first_line == 0 )
                    $first_line = $line_number;

                if( $first_line != $line_number )
                    $code .= "\n";

                $code .= $line;

            }

        }else{

            $code = 'File does not exist.';

        }

        $data['code'] = $code;
        $data['code_line'] = $code_line;
        $data['code_first_line'] = $code_first_line;

        return json_encode( $data );
    }
}
