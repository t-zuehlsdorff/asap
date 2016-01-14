<?php

declare(ticks = 1);

/**
  * this class stores callbacks in a queue and
  * execute each of them in a single process
  *
  * the maximum number of forks can be set,
  * to scale the number in relation to the
  * number of CPUs.
  *
  * to execute a callback, you must first
  * call addToQueue() and afterwards fork().
  * you have to call fork() until the queue
  * is empty and should use waitUntilForksFinished()
  * at the end, to make sure, everything
  * finished and you can get its result
  *
  * because multiple processes can be very tricky,
  * you should use setLogger and use the debug-information
  * while programming
  *
  **/
class Fork {

  /**
    * a callback to handle the logging-messages
    **/
  private $cloLogger = null;

  /**
    * maximum allowed number of parallel forks
    **/
  private $intMaxForks = 4;
  
  /**
    * number of currently active forks
    **/
  private $intActiveForks = 0;
  
  /**
    * list of callbacks which should be executed in forks
    **/
  private $arrQueue = array();
  
  /**
    * list of handler for signals. structure:
    * [signal] => handler
    **/
  private $arrSignalHandler = array();
  
  /**
    * constructor just initiates the default logger
    * which simply skips every log-message
    *
    **/
  public function __construct() {
  
    $this->cloLogger = function($strLogLine) { return; };
    
    pcntl_signal(SIGCHLD, array($this, 'handleSIGCHLD'));
  
  }
  
  /**
    * @param $cloLogger - logger to handle log messages of Fork
    *
    * @throw \Exception - if given parameter is not a callable
    *
    * set a callable to handle the log-messages of the Fork-class
    *
    **/
  public function setLogger($cloLogger) {
  
    if(!is_callable($cloLogger))
      throw new \Exception("given parameter is not callable");
    
    $this->cloLogger = $cloLogger;
  
  }

  /**
    * @param $intMaxForks - the maximum number of forks
    *
    * @throw \Exception - if given number is not an integer
    * @throw \Exception - if given number is not greater than 0
    *
    * set the allowed maximum number of parallel forks
    *
    **/
  public function setMaxForks($intMaxForks) {
  
    if(!is_int($intMaxForks))
      throw new \Exception("number of forks must be an integer!");
    
    if(0 >= $intMaxForks)
      throw new \Exception("number of forks must be greater than 0");
    
    $cloLogger = $this->cloLogger;
    $cloLogger("set max number of forks to $intMaxForks");
    
    $this->intMaxForks = $intMaxForks;
  
  }
  
  /**
    * @param $cloCallback - the callback to execute after forking
    *
    * @throw \Exception - if the given callback is not callable
    *
    * add a callback to the queue. for execution of the stored
    * callbacks you have to call Fork::fork() until the queue is empty
    *
    **/
  public function addToQueue($cloCallback) {
  
    if(!is_callable($cloCallback))
      throw new \Exception("given callback must be callable");
    
    $cloLogger = $this->cloLogger;
    $cloLogger("add new callback to queue");
    $this->arrQueue[] = $cloCallback;
  
  }
  
  /**
    * @returns (boolean) true, if queue is empty
    * @returns (boolean) false, if queue is not empty
    *
    * returns true if the queue is empty, otherwise false
    *
    **/
  public function isQueueEmpty() {
  
    if(empty($this->arrQueue))
      return true;
    
    return false;
  
  }
  
  /**
    * @param $intSignal   - the signal to store a handler for
    * @param $arrCallback - the callback to call when signal should be handled
    *
    * @throw \Exception - if the given signal is not an integer
    *
    * store a callback to handle a signal send by a fork
    *
    **/
  public function setSignalHandler($intSignal, array $arrCallback) {
  
    if(!is_int($intSignal))
      throw new \Exception("given signal must be an integer");
    
    $cloLogger = $this->cloLogger;
    $cloLogger("store new handler for signal: $intSignal");
    $this->arrSignalHandler[$intSignal] = $arrCallback;
  
  }
  
  /**
    * @return (boolean) false, if queue with callback is empty
    * @return (boolean) false, if maximum number of allowed forks is reached
    * @return (boolean) true, if the process is successfully forked
    *
    * fork the process and execute a queued callback
    *
    **/
  public function fork() {
  
    $cloLogger = $this->cloLogger;
    
    if($this->isQueueEmpty()) {
      $cloLogger("skip forking, queue is empty, nothing to fork");
      return false;
    }
    
    if($this->intActiveForks >= $this->intMaxForks) {
      $cloLogger("skip forking, maximum number of forks already active");
      return false;
    }
    
    $cloChildCallback = array_pop($this->arrQueue); # get from queue, before forking!
    
    $intPID = pcntl_fork();
    
    # if pid is not 0, its the pid of the fork and we are in the parent process
    if(0 !== $intPID) {
      $this->intActiveForks ++;
      $cloLogger("number of active forks: {$this->intActiveForks}");
      $cloLogger("forked child with PID: $intPID");
      return true;
    }
    
    # if pid is 0 we are in the child process
    # now the callback is executed in the fork
    if(0 === $intPID) {
      $intPID = posix_getpid();
      $cloLogger("child ($intPID): execute callback");
      $cloChildCallback();
      exit; # very important, otherwise the rest of the parent code is executed ;)
    }
  
  }
  
  /**
    * @param $intSleepIntervall - the number of milliseconds to sleep betweens the checks
    * @param $cloCallback       - callback to execute while waiting for childs to finish
    *
    * @throw \Exception - if given number is not an integer
    * @throw \Exception - if given number is not greater than 0
    * @throw \Exception - if given callback is not callable
    *
    * check if all forked childs are done and their processes finished.
    * if not:
    *  - if $cloCallback is definied, execute the callback *before* going to sleep
    *  - sleep the intervall given in $intSleepIntervall.
    *  - after finishing waiting, execute $cloCallback is definied
    *
    **/
  public function waitUntilForksFinished($intSleepIntervall = 100, $cloCallback = null) {
  
    if(!is_int($intSleepIntervall))
      throw new \Exception("number of milliseconds must be an integer!");
    
    if(0 >= $intSleepIntervall)
      throw new \Exception("number of milliseconds must be greater than 0");
      
    if(!is_null($cloCallback) && !is_callable($cloCallback))
      throw new \Exception("given callback is not callable!");
  
    $cloLogger = $this->cloLogger;
  
    # loop until every child has done!
    while (pcntl_waitpid(0, $intStatus, WNOHANG) != -1) {
    
      if(!is_null($cloCallback))
        $cloCallback();
    
      $cloLogger("waiting for childs: {$this->intActiveForks} childs left");
      usleep($intSleepIntervall);
      
    }
    
    # after waiting for all childs, execute callback a last time, if one exists
    if(!is_null($cloCallback))
        $cloCallback();
  
  }
  
  /**
    * this function is registered as default handler for SIGCHLD
    * it will decrease the number of active forks.
    * so do NOT call it directly, if you don't want more forks
    * than allowed!
    *
    **/
  public function handleSIGCHLD() {
  
    $cloLogger = $this->cloLogger;
    
    $intPID = pcntl_wait($intStatus);
    $cloLogger("child $intPID finished. reduce number of active forks");
    
    $this->intActiveForks --;
  
  }

}
