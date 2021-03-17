<?php

namespace HDSSolutions\Finpar\Interfaces;

interface Document {

    const STATUS_Draft      = 'DR';
    const STATUS_InProgress = 'IP';
    const STATUS_Approved   = 'AP';
    const STATUS_Rejected   = 'RE';
    const STATUS_Completed  = 'CO';
    const STATUS_Closed     = 'CL';
    const STATUS_Invalid    = 'IN';
    const STATUS_Unknown    = '??';
    const STATUSES = [
        self::STATUS_Draft,
        self::STATUS_InProgress,
        self::STATUS_Approved,
        self::STATUS_Rejected,
        self::STATUS_Completed,
        self::STATUS_Closed,
    ];

    const ACTION_Prepare    = 'PR';
    const ACTION_Approve    = 'AP';
    const ACTION_Complete   = 'CO';
    const ACTION_Reject     = 'RE';
    const ACTION_Close      = 'CL';
    const ACTION_ReOpen     = 'RO';
    const ACTIONS = [
        self::ACTION_Prepare,
        self::ACTION_Approve,
        self::ACTION_Complete,
        self::ACTION_Reject,
        self::ACTION_Close,
        self::ACTION_ReOpen,
    ];

    public function getDocumentStatusAttribute():string;

    public function setDocumentStatusAttribute(string $status):void;

    // public function setDocumentApprovedAttribute(bool $approved):void;

    public function documentError(?string $message = null):string;

    public function processIt(string $action):bool;

    // public function prepareIt():?string;

    // public function approveIt():?string;

    // public function rejectIt():?string;

    // public function completeIt():?string;

    // public function closeIt():?string;

    // public function reOpenIt():bool;

}
