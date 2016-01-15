<?php

namespace app\components;

use yii\console\Controller;

class MyJobController extends Controller
{
    protected $_periods = [
                                's' => [],
                                'm' => [],
                                'h' => [],
                        ];

    protected $_lastTime = 0;
    protected $_tickCount = 0;
    protected $_delay = 1;
    protected $_isDay = 0;

    // The signal handleres
    protected $_sigHandlers = [
        SIGHUP          => 'restart',
        SIGINT          => 'defaultSigHandler',
        SIGQUIT         => 'defaultSigHandler',
        SIGILL          => 'defaultSigHandler',
        SIGTRAP         => 'defaultSigHandler',
        SIGABRT         => 'defaultSigHandler',
        SIGIOT          => 'defaultSigHandler',
        SIGBUS          => 'defaultSigHandler',
        SIGFPE          => 'defaultSigHandler',
        SIGUSR1         => 'defaultSigHandler',
        SIGSEGV         => 'defaultSigHandler',
        SIGUSR2         => 'defaultSigHandler',
        SIGPIPE         => SIG_IGN,
        SIGALRM         => 'defaultSigHandler',
        SIGTERM         => 'stop',
        SIGCONT         => 'defaultSigHandler',
        SIGTSTP         => 'defaultSigHandler',
        SIGTTIN         => 'defaultSigHandler',
        SIGTTOU         => 'defaultSigHandler',
        SIGURG          => 'defaultSigHandler',
        SIGXCPU         => 'defaultSigHandler',
        SIGXFSZ         => 'defaultSigHandler',
        SIGVTALRM       => 'defaultSigHandler',
        SIGPROF         => 'defaultSigHandler',
        SIGWINCH        => 'defaultSigHandler',
        SIGIO           => 'defaultSigHandler',
        SIGBABY         => 'defaultSigHandler',
        SIG_BLOCK       => 'defaultSigHandler',
        SIG_UNBLOCK     => 'defaultSigHandler',
        SIG_SETMASK     => 'defaultSigHandler',
    ];

    /**
     * Sets the signal handlers
     *
     * @param int $signal signal number
     * @param ref $handler signal handler
     */
    public function setSigHandler($signal, $handler) {
        if (!isset($this->_sigHandlers[$signal])) {
            throw new Exception('Invalid signal number');
        }

        self::$_sigHandlers[$signal] = $handler;

        return true;
    }

    /**
     * Default signal handler
     *
     * @param int @sigNo signal number
     */
    public static function defaultSigHandler($sigNo) {
        switch ($sigNo) {
            case SIGTERM:
            case SIGINT:
                $this->stop();

                break;
            case SIGHUP:
                // Handle restart tasks
                $this->restart();

                break;
            default:
                // Handle all other signals
                break;
        }
    }

    public function init() {
        $this->setMemoryLimit();

        for($i = 1; $i <= 60; $i++) {
            $this->_periods['s'][$i] = array('value' => 0, 'last' => 0);
            $this->_periods['m'][$i] = array('value' => 0, 'last' => 0);
        }

        for($i = 1; $i <= 24; $i++) {
            $this->_periods['h'][$i] = array('value' => 0, 'last' => 0);
        }

        // TODO: see what it is possible to do with the signals

        /*foreach ($this->_sigHandlers as $signal => $handler) {
            if($signal) {
                if (!is_callable([$this, $handler]) && $handler != SIG_IGN && $handler != SIG_DFL) {
                    throw new \Exception('Uncallable signal');
                } else if (!pcntl_signal($signal, [get_class($this), $handler])) {
                    throw new \Exception('Unable to reroute signal handler (' . $signal . ')');
                }
            }
        }*/
    }

    public function setMemoryLImit() {

    }

    /**
     * Runs the job
     * @param string $jobName
     */
    public function actionJob($jobName) {
        $jobMethodName = 'job' . ucfirst($jobName);

        if (!method_exists($this, $jobMethodName)) throw new Exception('Job method ' . $jobMethodName . ' does not exist');

        $this->init();
        
        while (true) {
            $this->_tickCount ++;
            $this->checkTime();

            call_user_func([$this, $jobMethodName]);

            sleep($this->_delay);
        }
    }

    /**
     * Checks if given period type (s - seconds, m - minutes, h - hours) has passed
     */
    public function checkPeriod($type, $value) {
        if (array_search($type, ['s', 'm', 'h']) === false) throw new \Exception('Invalid period type: "' . $type . '"!');
        
        return !empty($this->_periods[$type][$value]['value']) ? true : false;
    } 

    /**
     * Updates the periods status
     */
    public function checkTime() {
        $this->_tickCount += time() - $this->_lastTime;

        if($this->_tickCount > 86400) {
            $this->_tickCount = 0;
            $this->_isDay = 1;
        } else {
            $this->_isDay = 0;
        }

        # Секундные интервалы
        for($s = 1; $s <= 60; $s++) {
            if(!$this->_tickCount) $this->_periods['s'][$s]['lasts'] = 0;

            if(intval($this->_tickCount / $s) && intval($this->_tickCount / $s) > $this->_periods['s'][$s]['last']) {
                $this->_periods['s'][$s]['value'] = 1;
                $this->_periods['s'][$s]['last'] = intval($this->_tickCount / $s);
            } else {
                $this->_periods['s'][$s]['value'] = 0;
            }
        }

        # Минутные
        for($m = 1; $m <= 60; $m++) {
            if(!$this->_tickCount) $this->_periods['m'][$m]['lasts'] = 0;

            if(intval($this->_tickCount / ($m * 60)) && intval($this->_tickCount / ($m * 60)) > $this->_periods['m'][$m]['last']) {
                $this->_periods['m'][$m]['value'] = 1;
                $this->_periods['m'][$m]['last'] = intval($this->_tickCount / ($m * 60));
            } else {
                $this->_periods['m'][$m]['value'] = 0;
            }
        }

        # Часовые
        for($h = 1; $h <= 24; $h++) {
            if(!$this->_tickCount) $this->_periods['h'][$h]['last'] = 0;

            if(intval($this->_tickCount / ($h * 3600)) > $this->_periods['h'][$h]['last']) {
                $this->_periods['h'][$h]['value'] = 1;
                $this->_periods['h'][$h]['last'] = intval($this->_tickCount / ($h * 3600));
            } else {
                $this->_periods['h'][$h]['value'] = 0;
            }
        }

        $this->_lastTime = time();
    }
}
