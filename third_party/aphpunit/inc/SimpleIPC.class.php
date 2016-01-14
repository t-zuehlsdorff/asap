<?php

/**
  * this class provides a very simple
  * interface for inter-process-messaging (IPC)
  *
  * it can be used to send messages between proceses,
  * by creating an instance of this class with the
  * same key. messages can be send() to a queue
  * where the will be stored until the are receive()d. ;)
  *
  * if the message-queue is no longer needed,
  * you should destroy them. otherwise the will resist
  * in memory until restart of the server!
  *
  * size of queue, number of messages in queue
  * and much more depends on the configuration of the
  * server.
  * it is very important to have the configuration right!
  *
  **/
class SimpleIPC {

  /**
    * the identifier of the queue
    **/
  private $intIdentifier = null;

  /**
    * the queue-resource to send and receive messages with
    **/
  private $resQueue = null;
  
  /**
    * the maximum size of a message
    * definied by server-configuration, have a look at the README
    **/
  private $intMaxMsgSize = null;

  /**
    * @param $intIdentifier - an integer to identify the wanted queue
    *
    * @throw \Exception - if identifier is not an integer
    * @throw \Exception - if queue could not be created/attached
    *
    * creates or attach a queue with the given identifier. if the queue
    * do not exists, it is created otherwise we attach us to the queue
    *
    **/
  public function __construct($intIdentifier) {
  
    if(!is_int($intIdentifier))
      throw new \Exception("given key must be an integer!");
    
    $this->intIdentifier = $intIdentifier;
    
    $this->attach();
  
  }
  
  /**
    * @param $strMessage   - message to send to the queue
    * @param $intErrorCode - reference to error-code, which is set in case of error
    *
    * @throw \Exception - if message is not a string
    *
    * @returns (boolean) true, if message was successfully send to queue
    * @returns (boolean) false, if error occured
    *
    * send the given message to the queue. if the message is to big,
    * the sending is blocked, until there is enough space in queue
    * to send the message.
    * 
    * if an error occurs, the parameter $intErrorCode will contain
    * the error-code.
    *
    **/
  public function send($strMessage, &$intErrorCode = null) {
  
    if(!is_string($strMessage))
      throw new \Exception("given parameter must be a string");
    
    return msg_send($this->resQueue, 1, $strMessage, false, true, $intErrorCode);
  
  }
  
  /**
    * @return (boolean) false, if queue is empty or another error occurs
    * @return (string) message received from queue
    *
    * receive the first message stored in the queue. if queue is empty
    * or an error occurs, false is returned. with receiving the
    * message is deleted from the queue.
    *
    **/
  public function receive() {
  
    if($this->isQueueEmpty())
      return false;
  
    msg_receive($this->resQueue, 0, $intMsgType, $this->intMaxMsgSize, $strMessage, false, MSG_IPC_NOWAIT);
  
    if(!is_string($strMessage))
      return false;
    
    return $strMessage;
    
  }
  
  /**
    * @return (boolean) true, if queue is empty
    * @return (boolean) false, if queue contains at least one message
    *
    * check if the message-queue is empty. if so, return true.
    * if at least on message is stored in queue false is returned
    **/
  public function isQueueEmpty() {
  
    $arrStatus = msg_stat_queue($this->resQueue);
    
    if(0 < $arrStatus['msg_qnum'])
      return false;
    
    return true;
  
  }
  
  /**
    * @return (boolean) true, if queue is attached
    * @return (boolean) false, if queue is not attached
    *
    * check if there is a queue attached
    *
    **/
  public function isAttached() {
    
    if(!is_resource($this->resQueue))
      return false;
    
    return true;
    
  }
  
  /**
    * @throw \Exception - if queue could not be created or attached
    *
    * attach to queue if it already exists or create a queue if not
    *
    **/
  public function attach() {
  
    $this->resQueue = msg_get_queue($this->intIdentifier);
    
    if(!is_resource($this->resQueue))
      throw new \Exception("could not create or attach queue for ipc");
    
    # get the maxium size of message and store it
    $arrStatus           = msg_stat_queue($this->resQueue);
    $this->intMaxMsgSize = $arrStatus['msg_qbytes'];
  
  }
  
  /**
    * destroy the queue; this also deletes all unreceived messages in queue.
    **/
  public function close() {
  
    return msg_remove_queue($this->resQueue);
  
  }
  
  /**
    * clear the queue by closing and recreating it. this will
    * delete all stored messages in queue
    **/
  public function clear() {
  
    $this->close();
    $this->attach();
  
  }

}
