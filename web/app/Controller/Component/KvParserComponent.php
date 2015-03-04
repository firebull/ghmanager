<?php
class KvParserComponent extends Component {
    protected $fhand;
    protected $fend = false;
    protected $comment = false;
    protected $turnoffcomment = false;
    protected $level = 0;
    protected $keyname = array();
    protected $keyset = array();
    protected $mykey = array();

    public function GetArray($config) {

        $lines = preg_split('/\n/', $config);
        $firstKey = true;
        $this->mykey = array();

        foreach ($lines as $line) {

            $pos = 0;
            $len = strlen($line);
            while ($pos<$len) {
                if ($this->turnoffcomment === true) {
                    $this->comment = false;
                    $this->turnoffcomment = false;
                }
                $char = substr ( $line , $pos, 1);
                if ($char === " " || $char === "\t" || $char === "\r" || $char === "\n" ) {$pos++; continue; }
                switch ($char) {
                    case "/":
                        $char2 = substr($line , $pos, 2);
                        if ($char2 === "/*") {
                            $this->comment = true;
                            break;
                        }
                        $char2 = substr ( $line , $pos-1, 2);
                        if ($char2 === "*/" && $this->comment === true ) {
                            $this->turnoffcomment = true;
                            break;
                        }

                }
                if ($this->comment) { $pos++; continue; }

                switch ($char) {
                    case "{":
                        $this->level++;
                        $this->keyset[$this->level] = false;
                        break;
                    case "}":
                        $this->level--;
                        $this->keyset[$this->level] = false;
                        break;
                    case "\"":
                        $pos2 = strpos($line , "\"", $pos+1);
                        $val = substr ($line, $pos+1, (($pos2-1)-($pos)));
                        $pos = $pos2;

                        if ($firstKey === true) {
                            $this->keyname[$this->level] = 'params';
                            $this->keyset[$this->level] = true;
                            $firstKey = false;
                        } elseif (empty($this->keyset[$this->level])) {
                            $this->keyname[$this->level] = $val;
                            $this->keyset[$this->level] = true;
                        } else {
                            $this->SetKeyVal($val, $this->level);
                            $this->keyset[$this->level] = false;
                        }

                }
                $pos++;
            }

        }

        return $this->mykey;
    }

    protected function SetKeyVal($val, $lvl) {
        $arr = array();
        $arr = $this->RecSet($val, $lvl, $arr);
        $this->mykey = array_merge_recursive($this->mykey, $arr);
    }

    protected function RecSet($val, $lvl, $array, $my=-1) {
        $my++;
        if ($my == $lvl) {
            @$array[$this->keyname[$my]] = $val;
        } else {
            @$array[$this->keyname[$my]] = $this->RecSet($val, $lvl, $array, $my);
        }
        return $array;
    }
}

?>
