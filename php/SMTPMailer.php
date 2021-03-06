<?php
require_once("smtp.php");
class SMTPMailer{
	/**
	* 0, 1, 2. See the details in smtp.php
	*/
	public $SMTPDebug=0;
	public $Host;
	public $Port=25;
	public $UserName;
	public $Password;
	public $HostName="localhost.localdomain";
	public $From;
	public $FromName="";
	/**
	* text/plain
	* text/html
	* multipart/alternative
	*/
	public $ContentType="text/plain";
	public $CharSet="utf-8";
	public $Subject;
	public $Body;
	public $To;
	public $Error="";
	private $id;
	private $header;
	private $body;
	private $smtp;
	public function Send(){
		$this->id=md5(uniqid(time()));
		$this->header="";
		$this->header.=sprintf("Date: %s\n",date("D, d M Y H:i:s T"));
		$this->header.=sprintf("Return-Path: %s\n",$this->From);
		$this->header.=sprintf("To: %s\n",$this->To);
		$this->header.=sprintf("From: %s <%s>\n",$this->FromName,$this->From);
		$this->header.=sprintf("Subject: =?%s?B?%s?=\n",$this->CharSet,base64_encode($this->Subject));
		$this->header.=sprintf("Message-ID: <%s@%s>\n",$this->id,$this->HostName);
		$this->header.="MIME-Version: 1.0\n";
		$this->header.="Content-Transfer-Encoding: base64\n";
		$this->header.=sprintf("Content-Type: %s; charset=\"%s\"",$this->ContentType,$this->CharSet);
		$this->header.="\n\n";
		$this->body=chunk_split(base64_encode($this->Body),76,"\n");
		$this->smtp=new smtp();
		$this->smtp->do_debug=$this->SMTPDebug;
		if(!$this->smtp->Connect($this->Host,$this->Port)){
			$this->Error="SMTP Connect False.";
			return false;
		}
		$this->smtp->Hello($this->HostName);
		if($this->smtp->Authenticate($this->UserName,$this->Password)){
			if($this->smtp->Mail($this->From)){
				if($this->smtp->Recipient($this->To)){
					if($this->smtp->Data($this->header.$this->body)){
						$this->smtp->Quit();
						$this->smtp->Close();
						return true;
					}else{
						$this->Error="SMTP Data False.";
						$this->smtp->Reset();
						return false;
					}
				}else{
					$this->Error="SMTP Recipient False.";
					$this->smtp->Reset();
					return false;
				}
			}else{
				$this->Error="SMTP Mail False.";
				$this->smtp->Reset();
				return false;
			}
		}else{
			$this->Error="SMTP Authenticate False.";
			$this->smtp->Reset();
			return false;
		}
	}
}
?>
