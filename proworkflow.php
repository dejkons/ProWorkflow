<?php
/**
* Creator: Dejan Adamovic
* Date: 28 Jun 2013
* Version: 1.0
* Description: ProworkFlow project management tool API class - provides essential functions for work with this API 
*/

class Proworkflow {
     
     const API_KEY = "3CFB-QT7N-YMW1-3KFH-PWFV261-EXAMPLE";
     const URL = "https://proworkflow1.net/something/api/example";
     const USER_ID = 11;
     
     #log file name
     private $log_file = "proworkflow_log.txt";
    
     function __construct() {}
     
     /**
     * Insert new job (project) into Proworkflow project manager
     * 
     * @param mixed $arrayValues
     * @return mixed array     Example array("ProjectID" => ID, "ProjectNumber" => ProjNumber);
     */
     public function insertJob($arrayValues) {
           
           $api_call = "addjob";
           $page_to_call = "jobs.cfm";
           
           $getParameters = array("customerkey" => self::API_KEY, "api_call" => $api_call, "UserID" => self::USER_ID);
           $webServiceUrl = self::URL . $page_to_call. '?' . http_build_query($getParameters); 
              
           // job assigned to staff
           $jobassignedtostaffArray = $arrayValues['jobassignedtostaff'];
           $jobassignedtostaff = "x";
           foreach ($jobassignedtostaffArray as $jobassignedtostaffId) {
                 $jobassignedtostaff .= $jobassignedtostaffId."x";   
           }
           
           if ($jobassignedtostaff == "x") {
                  $jobassignedtostaff = "";   
           }
           
           // job assigned to contractors
           $jobassignedtocontractorsArray = $arrayValues['jobassignedtocontractors'];
           $jobassignedtocontractors = "x";
           foreach ($jobassignedtocontractorsArray as $jobassignedtocontractorsId) {
                 $jobassignedtocontractors .= $jobassignedtocontractorsId."x";   
           }
           
           if ($jobassignedtocontractors == "x") {
                  $jobassignedtocontractors = "";   
           }
           
           // job assigned to clients
           $jobassignedtoclientsArray = $arrayValues['jobassignedtoclients'];
           $jobassignedtoclients = "x";
           foreach ($jobassignedtoclientsArray as $jobassignedtoclientsId) {
                 $jobassignedtoclients .= $jobassignedtoclientsId."x";   
           }
           
           if ($jobassignedtoclients == "x") {
                  $jobassignedtoclients = "";   
           }
         
           $xmlToSend = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                            <addjob>
                            <jobassignedtostaff>".$jobassignedtostaff."</jobassignedtostaff>
                            <jobmanager>".$arrayValues['jobmanager']."</jobmanager>
                            <jobassignedtocontractors>".$jobassignedtocontractors."</jobassignedtocontractors>
                            <jobassignedtoclients>".$jobassignedtoclients."</jobassignedtoclients>
                            <jobtotaltimeallocated>".$arrayValues['jobtotaltimeallocated']."</jobtotaltimeallocated>
                            <jobdatestart>".$arrayValues['jobdatestart']."</jobdatestart>
                            <jobdatedue>".$arrayValues['jobdatedue']."</jobdatedue>
                            <jobclientid>".$arrayValues['jobclientid']."</jobclientid>
                            <jobdescription>".$arrayValues['jobdescription']."</jobdescription>
                            <jobnumber>".$arrayValues['jobnumber']."</jobnumber>
                            <jobuseautonumber>".$arrayValues['jobuseautonumber']."</jobuseautonumber>
                            <jobtitle>".$arrayValues['jobtitle']."</jobtitle>
                            <jobcategoryid>".$arrayValues['jobcategoryid']."</jobcategoryid>
                            <jobinvoiced>".$arrayValues['jobinvoiced']."</jobinvoiced>
                            <jobpaid>".$arrayValues['jobpaid']."</jobpaid>
                            <jobaccountedfor>".$arrayValues['jobaccountedfor']."</jobaccountedfor>
                            <jobclientlogin>".$arrayValues['jobclientlogin']."</jobclientlogin>
                            <jobquotedprice>".$arrayValues['jobquotedprice']."</jobquotedprice>
                            <jobinvoicedprice>".$arrayValues['jobinvoicedprice']."</jobinvoicedprice>
                            <jobnotes>".$arrayValues['jobnotes']."</jobnotes>
                            <jobpriority>".$arrayValues['jobpriority']."</jobpriority>
                         </addjob>";
                                                 
            $ch = curl_init($webServiceUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_POST, 1);  
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlToSend);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);             
            
            
            $result = curl_exec($ch);
            
            if ($result === false) {
                  $error_log = 'Curl error [insertovanje novog projekta]: ' . curl_error($ch);
                  $this->saveLog($error_log);
                  curl_close($ch); 
                  return false;  
            } else {
                  curl_close($ch);   
            }
            
            $xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if (is_object($xml)) {
                   if ($xml->status == "Success") {
                         $error_log = "Uspesno insertovanje novog projekta u Proworkflow. Detalji projekta: ID = ".$xml->details->jobid." , Number = ".$xml->details->jobnumber;
                         $this->saveLog($error_log); 
                         return array("ProjectID" => $xml->details->jobid, "ProjectNumber" => $xml->details->jobnumber);
                   } else {
                         $error_log = "Neuspesno insertovanje novog projekta u Proworkflow [error type: ".$xml->status." - ".$xml->error."]";
                         $this->saveLog($error_log); 
                         return false;
                   }  
            } else {
                  $error_log = "Rezultat koji je vracen od Proworkflow-a nakon insertovanja novog projekta nije validan - neuspesno konvertovanje povratnog XML-a u objekat.";
                  $this->saveLog($error_log); 
                  return false;   
            }
     }
     
     /**
     * Insert new job request (project request) into Proworkflow project manager
     * 
     * @param mixed $arrayValues
     * @return mixed array     Example array("ProjectID" => ID, "ProjectMessage" => Message);
     */
     public function insertJobRequest($arrayValues) {
           
           $api_call = "addjobrequest";
           $page_to_call = "jobs.cfm";
           
           $getParameters = array("customerkey" => self::API_KEY, "api_call" => $api_call);
           $webServiceUrl = self::URL . $page_to_call. '?' . http_build_query($getParameters); 
              
           // job request assigned to
           $jobrequesttoArray = $arrayValues['jobrequestto'];
           $jobrequestto = "x";
           foreach ($jobrequesttoArray as $jobrequesttoId) {
                 $jobrequestto .= $jobrequesttoId."x";   
           }
           
           if ($jobrequestto == "x") {
                  $jobrequestto = "";   
           }
           
           // who asked for project request
           $jobrequestedbyArray = $arrayValues['jobrequestedby'];
           $jobrequestedby = "x";
           foreach ($jobrequestedbyArray as $jobrequestedbyId) {
                 $jobrequestedby .= $jobrequestedbyId."x";   
           }
           
           if ($jobrequestedby == "x") {
                  $jobrequestedby = "";   
           }
         
           $xmlToSend = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                            <addjobrerequest>
                            <jobrequestto>".$jobrequestto."</jobrequestto>
                            <jobrequestedby>".$jobrequestedby."</jobrequestedby>
                            <jobdatestart>".$arrayValues['jobdatestart']."</jobdatestart>
                            <jobdatedue>".$arrayValues['jobdatedue']."</jobdatedue>
                            <jobclientid>".$arrayValues['jobclientid']."</jobclientid>
                            <jobdescription>".$arrayValues['jobdescription']."</jobdescription>
                            <jobtitle>".$arrayValues['jobtitle']."</jobtitle>
                            <jobbudget>".$arrayValues['jobbudget']."</jobbudget>
                         </addjobrerequest>";
                                 
            $ch = curl_init($webServiceUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_POST, 1);  
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlToSend);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);             
            
            
            $result = curl_exec($ch);
            
            if ($result === false) {
                  $error_log = 'Curl error [insertovanje novog zahteva za projekat]: ' . curl_error($ch);
                  $this->saveLog($error_log);
                  curl_close($ch); 
                  return false;  
            } else {
                  curl_close($ch);   
            }
            
            $xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if (is_object($xml)) {
                   if ($xml->status == "Success") {
                         $error_log = "Uspesno insertovanje novog zahteva za projekat u Proworkflow. Detalji projekta: ID = ".$xml->details->jobid." , Message = ".$xml->details->message;
                         $this->saveLog($error_log); 
                         return array("ProjectID" => $xml->details->jobid, "ProjectMessage" => $xml->details->message);
                   } else {
                         $error_log = "Neuspesno insertovanje novog zahteva za projekat u Proworkflow [error type: ".$xml->status." - ".$xml->details->message."]";
                         $this->saveLog($error_log); 
                         return false;
                   }  
            } else {
                  $error_log = "Rezultat koji je vracen od Proworkflow-a nakon insertovanja novog zahteva za projekat nije validan - neuspesno konvertovanje povratnog XML-a u objekat.";
                  $this->saveLog($error_log); 
                  return false;   
            }           
                             
     }
     
     /**
     * Upload file into Proworkflow project manager
     * 
     * @param mixed $arrayValues
     * @return mixed array     Example array("FileID" => ID);
     */
     public function uploadFile($arrayValues) {
           
           $api_call = "uploadfile";
           $page_to_call = "files.cfm";
           
           $getParameters = array("customerkey" => self::API_KEY, "api_call" => $api_call, "UserID" => self::USER_ID);
           $webServiceUrl = self::URL . $page_to_call. '?' . http_build_query($getParameters); 
         
           $xmlToSend = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                         <uploadfile>
                                <jobid>".$arrayValues['jobid']."</jobid>
                                <filename>".$arrayValues['filename']."</filename>
                                <folderid>".$arrayValues['folderid']."</folderid>
                                <filedata>".$arrayValues['filedata']."</filedata>
                                <taskid>".$arrayValues['taskid']."</taskid>
                         </uploadfile>";
                                                 
            $ch = curl_init($webServiceUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_POST, 1);  
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlToSend);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);             
            
            
            $result = curl_exec($ch);
            
            if ($result === false) {
                  $error_log = 'Curl error [uplodovanje fajla]: ' . curl_error($ch);
                  $this->saveLog($error_log);
                  curl_close($ch); 
                  return false;  
            } else {
                  curl_close($ch);   
            }
            
            $xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if (is_object($xml)) {
                   if ($xml->status == "Success") {
                         $error_log = "Uspesno uplodovanje novog fajla u Proworkflow. Detalji fajla: ID = ".$xml->details->fileid." , Message = ".$xml->details->message." , ID Projekta = ".$arrayValues['jobid']." , Ime fajla = ".$arrayValues['filename'];
                         $this->saveLog($error_log); 
                         return array("FileID" => $xml->details->fileid);
                   } else {
                         $error_log = "Neuspesno uplodovanje novog fajla u Proworkflow [error type: ".$xml->status." - ".$xml->error."], [ime fajla: ".$arrayValues['filename']." , id projekta: ".$arrayValues['jobid']."]";
                         $this->saveLog($error_log); 
                         return false;
                   }  
            } else {
                  $error_log = "Rezultat koji je vracen od Proworkflow-a nakon uplodovanja novog fajla nije validan - neuspesno konvertovanje povratnog XML-a u objekat.";
                  $this->saveLog($error_log); 
                  return false;   
            }
            
     }
     
     /**
     * Create folder in Proworkflow project manager
     * 
     * @param mixed $arrayValues
     * @return mixed array     Example array("FileID" => ID);
     */
     public function createFolder($arrayValues) {
           
           $api_call = "createfolder";
           $page_to_call = "files.cfm";
           
           $getParameters = array("customerkey" => self::API_KEY, "api_call" => $api_call, "UserID" => self::USER_ID);
           $webServiceUrl = self::URL . $page_to_call. '?' . http_build_query($getParameters);
           
           
           // folder assigned to multiple projects
           $folderassginedtoArray = $arrayValues['folderassginedto'];
           $folderassginedto = "x";
           foreach ($folderassginedtoArray as $folderassginedtoId) {
                 $folderassginedto .= $folderassginedtoId."x";   
           }
           
           if ($folderassginedto == "x") {
                  $folderassginedto = "";   
           } 
         
           $xmlToSend = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                         <addfolder>
                                <jobid>".$arrayValues['jobid']."</jobid>
                                <foldername>".$arrayValues['foldername']."</foldername>
                                <publicfolder>".$arrayValues['publicfolder']."</publicfolder>
                                <folderassginedto>".$folderassginedto."</folderassginedto>
                         </addfolder>";
                                                 
            $ch = curl_init($webServiceUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_POST, 1);  
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlToSend);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);             
            
            
            $result = curl_exec($ch);
            
            if ($result === false) {
                  $error_log = 'Curl error [kreiranje foldera]: ' . curl_error($ch);
                  $this->saveLog($error_log);
                  curl_close($ch); 
                  return false;  
            } else {
                  curl_close($ch);   
            }
            
            $xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if (is_object($xml)) {
                   if ($xml->status == "Success") {
                         $error_log = "Uspesno kreiranje novog foldera u Proworkflow. Detalji foldera: ID = ".$xml->details->folderid." , Message = ".$xml->details->message." , ID Projekta = ".$arrayValues['jobid']." , Ime foldera = ".$arrayValues['foldername'];
                         $this->saveLog($error_log); 
                         return array("FolderID" => $xml->details->folderid);
                   } else {
                         $error_log = "Neuspesno kreiranje novog foldera u Proworkflow [error type: ".$xml->status." - ".$xml->error."], [ime foldera: ".$arrayValues['foldername']." , id projekta: ".$arrayValues['jobid']."]";
                         $this->saveLog($error_log); 
                         return false;
                   }  
            } else {
                  $error_log = "Rezultat koji je vracen od Proworkflow-a nakon kreiranja novog foldera nije validan - neuspesno konvertovanje povratnog XML-a u objekat.";
                  $this->saveLog($error_log); 
                  return false;   
            }
            
     }
     
     /**
     * Approve job request in Proworkflow project manager
     * 
     * @param mixed $jobId
     * @return mixed array     Example array("ProjectID" => jobId, "Message" => message);
     * 
     */
     public function approveJobRequest($jobId) {
            
            $jobId = (int)$jobId;
         
            $api_call = "approvejobrequest";
            $page_to_call = "jobs.cfm";
           
            $getParameters = array("customerkey" => self::API_KEY, "api_call" => $api_call, "UserID" => self::USER_ID, "JobId" => $jobId);
            $webServiceUrl = self::URL . $page_to_call. '?' . http_build_query($getParameters);
            
            $ch = curl_init($webServiceUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);             
            
            
            $result = curl_exec($ch);
            
            if ($result === false) {
                  $error_log = 'Curl error [odobravanje zahteva za novi projekat]: ' . curl_error($ch);
                  $this->saveLog($error_log);
                  curl_close($ch); 
                  return false;  
            } else {
                  curl_close($ch);   
            }
            
            $xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if (is_object($xml)) {
                   if ($xml->status == "Success") {
                         $error_log = "Uspesno odobravanje novog projekta u Proworkflow. Detalji projekta: ID = ".$jobId." , Message = ".$xml->details->message;
                         $this->saveLog($error_log); 
                         return array("ProjectID" => $jobId, "Message" => $xml->details->message);
                   } else {
                         $error_log = "Neuspesno odobravanje novog projekta u Proworkflow [error type: ".$xml->status." - ".$xml->details->message."]";
                         $this->saveLog($error_log); 
                         return false;
                   }  
            } else {
                  $error_log = "Rezultat koji je vracen od Proworkflow-a nakon odobravanja novog projekta nije validan - neuspesno konvertovanje povratnog XML-a u objekat.";
                  $this->saveLog($error_log); 
                  return false;   
            }  
     }
     
     /**
     * Decline job request in Proworkflow project manager
     * 
     * @param mixed $jobId
     * @return mixed array     Example array("ProjectID" => jobId, "Message" => message);
     * 
     */
     public function declineJobRequest($jobId) {
            
            $jobId = (int)$jobId;
         
            $api_call = "declinejobrequest";
            $page_to_call = "jobs.cfm";
           
            $getParameters = array("customerkey" => self::API_KEY, "api_call" => $api_call, "UserID" => self::USER_ID, "JobId" => $jobId);
            $webServiceUrl = self::URL . $page_to_call. '?' . http_build_query($getParameters);
            
            $ch = curl_init($webServiceUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);             
            
            
            $result = curl_exec($ch);
            
            if ($result === false) {
                  $error_log = 'Curl error [odbijanje zahteva za novi projekat]: ' . curl_error($ch);
                  $this->saveLog($error_log);
                  curl_close($ch); 
                  return false;  
            } else {
                  curl_close($ch);   
            }
            
            $xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if (is_object($xml)) {
                   if ($xml->status == "Success") {
                         $error_log = "Uspesno odbijanje novog projekta u Proworkflow. Detalji projekta: ID = ".$jobId." , Message = ".$xml->details->message;
                         $this->saveLog($error_log); 
                         return array("ProjectID" => $jobId, "Message" => $xml->details->message);
                   } else {
                         $error_log = "Neuspesno odbijanje novog projekta u Proworkflow [error type: ".$xml->status." - ".$xml->details->message."]";
                         $this->saveLog($error_log); 
                         return false;
                   }  
            } else {
                  $error_log = "Rezultat koji je vracen od Proworkflow-a nakon odbijanja novog projekta nije validan - neuspesno konvertovanje povratnog XML-a u objekat.";
                  $this->saveLog($error_log); 
                  return false;   
            }  
     }
     
     /**
     * Get all job categories from Proworkflow project manager
     * 
     * @return mixed array of objects
     * @return false if error
     * 
     */
     public function getAllJobCategories() {
                     
            $api_call = "jobcategories";
            $page_to_call = "jobs.cfm";
           
            $getParameters = array("customerkey" => self::API_KEY, "api_call" => $api_call);
            $webServiceUrl = self::URL . $page_to_call. '?' . http_build_query($getParameters);
            
            $ch = curl_init($webServiceUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);             
            
            
            $result = curl_exec($ch);
            
            if ($result === false) {
                  $error_log = 'Curl error [dobijanje liste svih kategorija]: ' . curl_error($ch);
                  $this->saveLog($error_log);
                  curl_close($ch); 
                  return false;  
            } else {
                  curl_close($ch);   
            }
            
            $xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if (is_object($xml)) {
                   if ($xml->status == "OK") {
                         $error_log = "Uspesno dobijanje liste svih kategorija iz Proworkflow - a.";
                         $this->saveLog($error_log); 
                         return $xml;
                   } else {
                         $error_log = "Neuspesno dobijanje liste svih kategorije";
                         $this->saveLog($error_log); 
                         return false;
                   }  
            } else {
                  $error_log = "Rezultat koji je vracen od Proworkflow-a nakon dobijanja liste svih kategorija nije validan - neuspesno konvertovanje povratnog XML-a u objekat.";
                  $this->saveLog($error_log); 
                  return false;   
            }  
     }
     
     /**
     * Get all contacts from Proworkflow project manager
     * 
     * @param $contactType    1 - Client, 2 - Contractor, 3 - Staff, 4 - Other   , 0 (empty) - All
     * @return mixed array of objects
     * @return false if error
     * 
     */
     public function getAllContacts($contactType = 0) {
                     
            $api_call = "allcontacts";
            $page_to_call = "contacts.cfm";
           
            $getParameters = array("customerkey" => self::API_KEY, "api_call" => $api_call);
            
            if ($contactType != 0) {
                   $getParameters['ContactTypeID'] = $contactType;  
            }
            
            $webServiceUrl = self::URL . $page_to_call. '?' . http_build_query($getParameters);
            
            $ch = curl_init($webServiceUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);             
            
            
            $result = curl_exec($ch);
            
            if ($result === false) {
                  $error_log = 'Curl error [dobijanje liste svih kontakata]: ' . curl_error($ch);
                  $this->saveLog($error_log);
                  curl_close($ch); 
                  return false;  
            } else {
                  curl_close($ch);   
            }
            
            $xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if (is_object($xml)) {
                   if ($xml->status == "OK") {
                         $error_log = "Uspesno dobijanje liste svih kontakata iz Proworkflow - a.";
                         $this->saveLog($error_log); 
                         return $xml;
                   } else {
                         $error_log = "Neuspesno dobijanje liste svih kontakata iz Proworkflow - a";
                         $this->saveLog($error_log); 
                         return false;
                   }  
            } else {
                  $error_log = "Rezultat koji je vracen od Proworkflow-a nakon dobijanja liste svih kontakata nije validan - neuspesno konvertovanje povratnog XML-a u objekat.";
                  $this->saveLog($error_log); 
                  return false;   
            }  
     }
     
     /**
     * Get all jobs from Proworkflow project manager
     * 
     * @return mixed array of objects
     * @return false if error
     * 
     */
     public function getAllJobs() {
                     
            $api_call = "alljobs";
            $page_to_call = "jobs.cfm";
           
            $getParameters = array("customerkey" => self::API_KEY, "api_call" => $api_call, "UserID" => self::USER_ID);
            
            $webServiceUrl = self::URL . $page_to_call. '?' . http_build_query($getParameters);
            
            $ch = curl_init($webServiceUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);             
            
            
            $result = curl_exec($ch);
            
            if ($result === false) {
                  $error_log = 'Curl error [dobijanje liste svih projekata]: ' . curl_error($ch);
                  $this->saveLog($error_log);
                  curl_close($ch); 
                  return false;  
            } else {
                  curl_close($ch);   
            }
            
            $xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if (is_object($xml)) {
                   if ($xml->status == "OK") {
                         $error_log = "Uspesno dobijanje liste svih projekata iz Proworkflow - a.";
                         $this->saveLog($error_log); 
                         return $xml;
                   } else {
                         $error_log = "Neuspesno dobijanje liste svih projekata iz Proworkflow - a";
                         $this->saveLog($error_log); 
                         return false;
                   }  
            } else {
                  $error_log = "Rezultat koji je vracen od Proworkflow-a nakon dobijanja liste svih projekata nije validan - neuspesno konvertovanje povratnog XML-a u objekat.";
                  $this->saveLog($error_log); 
                  return false;   
            }  
     }
     
     /**
     * Get specific job from Proworkflow project manager
     *
     * @param $jobId 
     * @return mixed object
     * @return false if error
     * 
     */
     public function getSpecificJob($jobId = 0) {
                     
            $api_call = "specificjob";
            $page_to_call = "jobs.cfm";
           
            $getParameters = array("customerkey" => self::API_KEY, "api_call" => $api_call);
            
            $returnObject = new stdClass();
            
            if ($jobId != 0) {
                   $getParameters['JobId'] = $jobId;  
            } else {
                   $error_log = "Neuspesno dobijanje odredjenog projekta iz Proworkflow - a. ID projekta nije prosledjen";
                   $this->saveLog($error_log);
                   return false;   
            }
            
            $webServiceUrl = self::URL . $page_to_call. '?' . http_build_query($getParameters);
            
            $ch = curl_init($webServiceUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);             
            
            
            $result = curl_exec($ch);
            
            if ($result === false) {
                  $error_log = 'Curl error [dobijanje podataka odredjenog projekta]: ' . curl_error($ch);
                  $this->saveLog($error_log);
                  curl_close($ch); 
                  return false;  
            } else {
                  curl_close($ch);   
            }
            
            $xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if (is_object($xml)) {
                   if ($xml->status == "OK") {
                         $error_log = "Uspesno dobijanje podataka odredjenog projekta iz Proworkflow - a. ID trazenog projekta je : ". $jobId;
                         $this->saveLog($error_log); 
                         return $xml;
                   } else {
                         $error_log = "Neuspesno dobijanje podataka odredjenog projekta Proworkflow - a. ID trazenog projekta je : ". $jobId;
                         $this->saveLog($error_log); 
                         return false;
                   }  
            } else {
                  $error_log = "Rezultat koji je vracen od Proworkflow-a nakon dobijanja podataka odredjenog projekta nije validan - neuspesno konvertovanje povratnog XML-a u objekat.";
                  $this->saveLog($error_log); 
                  return false;   
            }  
     }
     
     /**
     * Get all active job requests from Proworkflow project manager
     *
     * @return mixed object
     * @return false if error
     * 
     */
     public function getAllActiveJobRequests() {
                     
            $api_call = "activejobrequests";
            $page_to_call = "jobs.cfm";
           
            $getParameters = array("customerkey" => self::API_KEY, "api_call" => $api_call, "UserID" => self::USER_ID);
            
            $webServiceUrl = self::URL . $page_to_call. '?' . http_build_query($getParameters);
            
            $ch = curl_init($webServiceUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);             
            
            
            $result = curl_exec($ch);
            
            if ($result === false) {
                  $error_log = 'Curl error [dobijanje liste aktivnih zahteva za projekat]: ' . curl_error($ch);
                  $this->saveLog($error_log);
                  curl_close($ch); 
                  return false;  
            } else {
                  curl_close($ch);   
            }
            
            $xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if (is_object($xml)) {
                   if ($xml->status == "OK") {
                         $error_log = "Uspesno dobijanje liste svih aktivnih zahteva za projekat iz Proworkflow - a.";
                         $this->saveLog($error_log); 
                         return $xml;
                   } else {
                         $error_log = "Neuspesno dobijanje liste svih aktivnih zahteva za projekat iz Proworkflow - a";
                         $this->saveLog($error_log); 
                         return false;
                   }  
            } else {
                  $error_log = "Rezultat koji je vracen od Proworkflow-a nakon trazenja liste svih aktivnih zahteva za projekat nije validan - neuspesno konvertovanje povratnog XML-a u objekat.";
                  $this->saveLog($error_log); 
                  return false;   
            }  
     }
     
     /**
     * Get all companies from Proworkflow project manager
     * 
     * @return mixed array of objects
     * @return false if error
     * 
     */
     public function getAllCompanies() {
                     
            $api_call = "allcompanies";
            $page_to_call = "contacts.cfm";
           
            $getParameters = array("customerkey" => self::API_KEY, "api_call" => $api_call);
            $webServiceUrl = self::URL . $page_to_call. '?' . http_build_query($getParameters);
            
            $ch = curl_init($webServiceUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);             
            
            
            $result = curl_exec($ch);
            
            if ($result === false) {
                  $error_log = 'Curl error [dobijanje liste svih kompanija]: ' . curl_error($ch);
                  $this->saveLog($error_log);
                  curl_close($ch); 
                  return false;  
            } else {
                  curl_close($ch);   
            }
            
            $xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            if (is_object($xml)) {
                   if ($xml->status == "OK") {
                         $error_log = "Uspesno dobijanje liste svih kompanija iz Proworkflow - a.";
                         $this->saveLog($error_log); 
                         return $xml;
                   } else {
                         $error_log = "Neuspesno dobijanje liste svih kompanija";
                         $this->saveLog($error_log); 
                         return false;
                   }  
            } else {
                  $error_log = "Rezultat koji je vracen od Proworkflow-a nakon dobijanja liste svih kompanija nije validan - neuspesno konvertovanje povratnog XML-a u objekat.";
                  $this->saveLog($error_log); 
                  return false;   
            }  
     }
     
     /**
     * Save log
     *
     * @param $log_text     Text to be stored in log file
     * @return void
     * 
     */
     private function saveLog($log_text) {
            file_put_contents($this->log_file, "[ ".date("d.m.Y H:i:s")." ]".$log_text."\r\n", FILE_APPEND | LOCK_EX);   
     }
     
}  
?>
