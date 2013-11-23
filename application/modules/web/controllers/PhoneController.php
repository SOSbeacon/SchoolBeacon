<?php

class Web_PhoneController extends Zend_Controller_Action {

    public function indexAction() {
        
    }
    
    /*
     * Sync SOSschool contact with SOSbeacon contact
     */
    public function syncAction() {
        $logger = Sos_Service_Logger::getLogger();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout() ->disableLayout();
        $schoolId = (int )$this->_request->getParam('SOSschoolId', '');
        $schoolUserId = (int ) $this->_request->getParam('SOSschoolUserId', '');
        $schoolUserName =  trim(urldecode($this->_request->getParam('SOSschoolUserName', '')));
        $groupName = trim(urldecode($this->_request->getParam('groupName', '')));
        $sosPhone = trim(urldecode($this->_request->getParam('sosPhone', '')));
        $sosPhone = Sos_Service_Functions::stripPhoneNumber($sosPhone);
        $contactString = trim(urldecode($this->_request->getParam('contacts', '')));
        $key = trim($this->_request->getParam('syncKey', ''));
        $contacts = unserialize($contactString);
        $logger->log(">>>> START SYNC CONTACT FROM SOSschool.
            schoolId:$schoolId,schoolUserId:$schoolUserId,schoolUserName:$schoolUserName,groupName:$groupName,sosPhone:$sosPhone,total contacts:" . count($contacts), Zend_Log::INFO);
        $message = '';
        $syncKey = 'sosschool0028bd9bec29bf718b1e275d1ca04362';
        if (($key == $syncKey) && $schoolId && $schoolUserId && $schoolUserName && $groupName && $sosPhone && (count($contacts) > 0)) {
            // find phone account by number
            $phone = new Sos_Model_Phone();
            $phoneMapper = new Sos_Model_PhoneMapper();
            $phones = $phoneMapper->findByField('number', $sosPhone, $phone);
            $phoneId = 0;
            if (count($phones)) {
                $phone = $phones[0];
                $phoneId = $phone->getId();
            }
            if ($phoneId) {
                $group = new Sos_Model_Contactgroup();
                $groupMapper = new Sos_Model_ContactgroupMapper();
                $contact = new Sos_Model_Contact();
                $contactMapper = new Sos_Model_ContactMapper();
                // find phone group by name
                $groups = $groupMapper->fetchList("phone_id =$phoneId AND LOWER(name)='" . strtolower($groupName) . "'");
                if (count($groups) > 0) {
                    $group = $groups[0];
                } else {
                    // create new group
                    $group->setName($groupName)->setPhoneId($phoneId)->setType(3);
                    $group->save();
                }
                $groupId = $group->getId();
                // delete exist contact in group
                $contact->delete("group_id=$groupId AND type<>1");
                $successCount = 0;
                foreach($contacts as $c) {
                    $contactName = strip_tags($c['name']);
                    $email = $c['email'];
                    $textphone = Sos_Service_Functions::stripPhoneNumber($c['textphone']);
                    $voicephone = Sos_Service_Functions::stripPhoneNumber($c['voicephone']);
                    $contact = new Sos_Model_Contact(); // create new contact
                    $contact->setName($contactName)
                            ->setEmail($email)
                            ->setTextphone($textphone)
                            ->setVoicephone($voicephone)
                            ->setGroupId($groupId)
                            ->setType('0')
                            ->save();
                    $successCount++;
                }
                $message .= "SOSbeacon contact sync successfully, $successCount contacts saved.";
            } else {
                $message .= "SOSbeacon phone number $sosPhone is not exist.";
            }
        } else {
            $message .= 'Request params are not valid.';
        }
        $logger->log("SYNC result message: $message", Zend_log::INFO);
        echo $message;
    }
    
    public function importAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $importList = $this->list2;
        $rows = explode(chr(10), $importList);
        $count = 0;
        $savedCount = 0;
        $phoneId = 1776;
        $groupModel = new Sos_Model_Contactgroup();
        $groupMapper = new Sos_Model_ContactgroupMapper();
        $output = '<table border="1"><tr><td>#</td><td>Name</td><td>Email</td><td>Voice phone</td><td>Voice phone saved</td><td>Textphone</td><td>Textphone saved</td><td>Group</td><td>Group saved</td></tr>';
        foreach ($rows as $r) {
            $d = explode(',', $r);
            $count++;
            $n = trim($d[1]);
            $e = trim($d[2]);
            $vp = trim($d[3]);
            $tp = trim($d[4]);
            $g = trim($d[0]);
            $new_tp = Sos_Service_Functions::stripPhoneNumber($tp);
            $new_vp = Sos_Service_Functions::stripPhoneNumber($vp);
            $groups = $groupMapper->fetchList("phone_id=$phoneId AND name='$g'");
            $groupName = '';
            if (count($groups)) {
                $group = $groups[0];
                $groupName = $group->getName();
            }
            $output .= "<tr><td>$count</td><td>$n</td><td>$e</td><td>$vp</td><td>$new_vp</td><td>$tp</td><td>$new_tp</td><td>$g</td><td>$groupName</td></tr>";
            if ($this->getRequest()->getParam('do', '') == 'imp') {
                if (count($groups)) {
                    $group = $groups[0];
                    $contact = new Sos_Model_Contact();
                    $contact->setName($n);
                    $contact->setEmail($e);
                    $contact->setVoicephone($new_vp);
                    $contact->setTextphone($new_tp);
                    $contact->setType('0');
                    $contact->setGroupId($group->getId());
                    /// $contact->save(); //## Check carefully before import contact
                    $savedCount++;
                }
            }
        }
        $output .= "</table>";
        echo "savedCount $savedCount";
        echo $output;
        
    }
    
    public $list2 = 'Zayit,Michelle Gomez,mgomez.sf@gmail.com,510-208-5940,415-407-2407
Zayit,Julie Elis,julieelis2008@gmail.com,,415-602-7187
Zayit,Nicole Herron,ncherron@sbcglobal.net,510-559-9281,415-314-1847
Zayit,Laura Gruenwad,laura.gruenwald@gmail.com,,510-725-7101
Zayit,Nirit Sapir,nirithaviv@yahoo.com,510-528-4710,510-847-6380
Zayit,Edward Hieatt,edwardh@gmail.com,,415-269-2347
Zayit,Jen Isacoff,jen.isacoff@yahoo.com,510-558-0760,650-269-2065
Gefen,Michelle Schorr,michelleanddave@gmail.com,415-566-5635,415-816-4587
Gefen,Sara Hinkley,hinkleysara@gmail.com,,415-515-7789
Gefen,Michael Marx,mmrxster@gmail.com,510-547-0995,510-472-7661
Gefen,Jonathan Ajo-Franklin,jbajo-franklin@lbl.gov,510-848-5106,510-735-4350
Gefen,Nina Aron,nina.renata@gmail.com,,510-206-6611
Gefen,Leah Simon-Weisberg,leahfsw@gmail.com,,323-842-8614
Gefen,Allie Frank,allie.frank@gmail.com,510-558-8100,510-928-0715
Gefen,Carolyn Bertozzi,crb@berkeley.edu,,510-499-7053
Gefen,Samantha Cooper,samanthacooper@yahoo.com,,510-435-7159
Gefen,Hannah Nystrom,hannahNystrom@me.com,,415-568-5425
Gefen,Franklin Adler,jcceb@bearquest.com,510-849-9933,510-501-4089
Gefen,Erica Klempner,ericaklempner@yahoo.com,510-644-2604,510-502-7444
Alon,Kate Lauer,katil@mindspring.com,510-704-8712,646-643-5283
Alon,Tong Xiao,tfishgfp@hotmail.com,510-898-1094,510-388-6100
Alon,Chelsea Toller,chelsea.toller@gmail.com,408-608-5208,408-489-6840
Alon,Shana Hurowitz,shurowitz2003@yahoo.com,,774-232-0150
Alon,Tong Xiao,tfishgfp@hotmail.com,510-898-1094,510-388-6100
Alon,Belinda Newman,belindasf@gmail.com,510-524-3204,415-845-5259
Alon,Leah Smithers,leah.smithers@gmail.com,510-647-8480,510-823-4117
Alon,Renata Fineberg,fineberg@covad.net,510-548-2532,510-715-0443
Alon,Allison Wren,awren@sbcglobal.net,510-558-7084,510-499-0657
Alon,Offer Grembek,grembek@yahoo.com,510-559-7290,510-386-4904
Alon,Carolyn von Behren,cvonbehren@gmail.com,510-540-1161,510-388-5492
Alon,Lacy Cline,lacycline@hotmail.com,510-647-9882,510-504-6130
Alon,Leslie Gray,lesmgray@gmail.com,510-558-0244,415-596-9953
Alon,Michael Schwarz,schwarz.m@gmail.com,,510-409-2675
Alon,Michael Hatch,hatchmike65@yahoo.com,,510-717-1045
Alon,Ana Bellomo,abellomo@gmail.com,510-868-0575,510-417-9390
Alon,Anna Talamo ,atalamo@jps.net,510-528-6856,510-207-3670
Alon,Becka Lee,beckallee@yahoo.com,,510-508-3667
Alon,Hillary Brooks,hillary.maroon@gmail.com,510-665-7859,510-457-5748
Alon,Itmar Orgad,orgadim@gmail.com,,510-847-1413
Alon,Miri Levy,miralevy@comcast.net,510-778-8769,510-501-9880
Alon,Deborah Simon-Weisberg,DrDeborahsw@yahoo.com,,
Rimon,Samantha Cooper,samanthacooper@yahoo.com,,510-435-7159
Rimon,Michael Schwarz,schwarz.m@gmail.com,,510-409-2675
Rimon,Karen Kelley,karkai@pacbell.net,510-540-1235,415-260-9172
Rimon,Tommy Kaplan,tomkap@gmail.com,510-558-0264,510-356-7005
Rimon,Andrea Scher Passmore,andrea@superherodesigns.com,510-984-1178,415-572-6552
Rimon,Ellen Weinreb,ellenbethw@yahoo.com,510-524-3585,510-542-4533
Rimon,Mederick Ravel,mederick@gmail.com,510-868-4361,415-999-9082
Rimon,Samantha Cooper,samanthacooper@yahoo.com,,510-435-7159
Rimon,Jihyun Kim,lovetantan@gmail.com,510-528-8744,512-965-7397
Rimon,Lily Monti,lilymonti@hotmail.com,,510-326-3132
Rimon,Jonathan L. Aaron,runawayjim19@yahoo.com,510-524-1182,510-508-0576
Rimon,Michael Toth,mike@spiralcraft.com,,650-218-4784
Rimon,James Davis,poucajim@yahoo.com,,510-529-6317
Rimon,Sara Gemes,saranewsee36@hotmail.com,510-978-0608,
Rimon,Ariel Lustig,arilustig@aim.com,510-559-3969,415-244-8667
Rimon,Jodi Ravel,mederick@gmail.com,510-868-4361,415-999-9082
Rimon,Timna Ziv,timnaziv@hotmail.com,510-525-4172,510-847-4178
Rimon,Kim Robinson,kimrob47@gmail.com,,928-420-9822
Rimon,Tanya Pearlman,tpearlman@aol.com,510-705-1494,510-421-2272
Rimon,Katrina Spencer,katrinaspencer@gmail.com ,510-204-9044,510-529-5708
Rimon,Elizah Noh,elizanoh@hotmail.com,510-883-0804,714-381-3236
HaGesher,Sadie Costello,sadiecash@gmail.com,,310-428-0754
HaGesher,Rebecca Brams,rbrams@earthlink.net,510-848-2425,760-812-0577
HaGesher,Dan Ross,dan@dangodan.com,510-705-1365,510-967-9887
HaGesher,Jennifer Lagaly,Jlagaly@saleforce.com,,415-637-2687
HaGesher,Michelle Gomez,mgomez.sf@gmail.com,510-208-5940,415-407-2407
Rainbow,Sphie Tong,tongjj.pku@gmail.com ,510-527-9438,510-388-1920
Test,Cliff 5278898-yahoo,clifforloff@yahoo.com,510-540-9446,510-527-8898
Zayit,Jon Pendleton,j1pc@pge.com,415-973-2916,415-971-8064
Zayit,Roy Elis,royelis@gmail.com,917-902-2333,650-799-1596
Zayit,Matt Wilson,,408-299-7148,510-206-5347
Zayit,Michael Gruenwald,michael.gruenwald@berkeley.edu,510-643-1018,510-725-7100
Zayit,Nir Sapir,nir.sapir@mail.huji.ac.il,,510-295-9952
Zayit,Klara Prokopcova,klarka@gmail.com,,415-305-8243
Zayit,Ehud Isacoff,ehud@berkeley.edu,510-642-9853,510-684-4345
Gefen,Dave Schorr,michelleanddave@gmail.com,,
Gefen,Phil Walz,philwalz@gmail.com,,415-652-6173
Gefen,Anne Marx,asmarx1@gmail.com,415-865-7690,510-703-9956
Gefen,Carolyn Ajo-Franklin,Cajo-Franklin@lbl.gov,510-486-4299,510-220-2769
Gefen,Zachary Judkins,zac.steele@gmail.com,510-260-8347,415-846-7674
Gefen,John Slattery,zween.works@gmail.com,,323-842-8615
Gefen,Monica Kaba,mgkaba@gmail.com,,510-527-1334
Gefen,Philip Soffer,phsoffer@yahoo.com,,510-435-7175
Gefen,Rick Nystrom,RNYstrom@gmail.com,,408-206-5555
Gefen,Carol Loo,jcceb@bearquest.com,,510-517-9934
Gefen,Mathew Kessler,mat@geech.com,,415-606-4294
Alon,Mati Teiblum,teiblum@mindspring.com,415-788-6606,347-615-4766
Alon,Michael Cohn,michaelbcohn@gmail.com,,510-207-2169
Alon,Gunthar Hartwig,gunthar@gunthar.com,408-499-7826,408-499-7826
Alon,Michael Cohn,michaelbcohn@gmail.com,,510-207-2169
Alon,Dan Newman,dan@sayican1.com,510-848-0894,510-290-3921
Alon,Juston Smithers,justonwo@yahoo.com,,510-393-1526
Alon,Sydney Dietz,,,510-209-3654
Alon,Darrin Banks,darrinbanks@ymail.com,925-299-9939,925-997-6044
Alon,Mirit Grembek,miritsela@yahoo.com,,510-386-4902
Alon,Rob von Behren,,,510-992-6311
Alon,Jake Cline,jbcline77@hotmail.com,510-464-8068,510-506-9779
Alon,Marc Freedman,marc.freedman@yahoo.com,,415-336-4579
Alon,Lydia Lopez,lopez06@gmail.com,,
Alon,Betsy Hatch,ellzbth@hotmail.com,510-879-1020,510-866-4825
Alon,Joshua Bloom,jbloom@astro.berkeley.edu,,510-229-8163
Alon,Jonathan Lipschutz,berkeleysushiman@gmail.com,650-259-9748,415-595-2405
Alon,Greg Bass,x4bikes@yahoo.com,510-418-1452,510-922-1177
Alon,Ifat Orgad,,,510-847-6594
Rimon,Philip Soffer,phsoffer@yahoo.com,,510-435-7175
Rimon,Lydia Lopez,lopez06@gmail.com,,
Rimon,Dalit May,dalitmay@gmail.com,,510-356-7006
Rimon,Matthew Passmore,matt@rebargroup.org,,415-676-9035
Rimon,Charlie Haims,charliehaims@gmail.com,,415-939-9903
Rimon,Jodi Ravel,jodi.ravel@gmail.com,510-625-6306,415-999-5915
Rimon,Philip Soffer,phsoffer@yahoo.com,,510-435-7175
Rimon,Peter Fredman,peterfredman@gmail.com,,510-414-5432
Rimon,Danielle Thiry-Zaragoza,dtzaragoza@gmail.com,415-268-6041,510-508-0734
Rimon,Rachel Henderson,rachel.henderson@gmail.com,510-643-4746,510-209-9095
Rimon,Juliana Fredman,Julianaf59@hotmail.com,510-843-9130,510-529-6277
Rimon,Albert Reinhardt,,,415-244-8335
Rimon,Jodi Ravel,jodi.ravel@gmail.com,510-625-6306,415-999-5915
Rimon,Yanay Farja,yefarja@usfca.edu,,510-859-3250
Rimon,duffmcgee,duffmcgee@msn.com,,925-348-0990
Rimon,Grant Reading,kiaora_nz@yahoo.com,,510-410-8138
Rimon,Doug Spencer,dougspencer@gmail.com,,510-529-5709
Rimon,Steven Morrison,morri_69@hotmail.com,,510-229-0721
HaGesher,Josh Costello,joshcostello@gmail.com,,415-830-6385
HaGesher,Mikhail Davis,mikhail.davis@gmail.com,415-652-3099,
HaGesher,Kim Miller, kimemiller@yahoo.com,,510-601-8289
HaGesher,Kevin Vasquez,ksvasquez@hotmail.com,,415-218-5264
HaGesher,Jon Pendleton,j1pc@pge.com,415-973-2916,415-971-8064
Test,Cliff 9164755596-mit,clifforloff@alum.mit.edu,,916-475-5596';
    
    public function settingsAction() {
        $phoneId = $this->_request->getParam('pid', false);
        $layoutType = $this->_request->getParam('layout', false);
        if ($layoutType =='off') {
            $this->_helper->layout()->disableLayout();
        }
        $phone = new Sos_Model_Phone();
        $phoneMap = new Sos_Model_PhoneMapper();
        $contact = new Sos_Model_Contact();
        $contactMap = new Sos_Model_ContactMapper();

        $auth = Sos_Service_Functions::webappAuth(false);
        //if authentication false, not show alertlog
        if (!$auth->getId() && !$layoutType) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/web/users/login/");
        } 
        if ($auth->getId()) {
            $phoneMap->find($auth->getId(), $phone);
            if (!$phoneId) {
                $phoneId = $auth->getId();
            }
        }
        $contact = new Sos_Model_Contact();
        $contactMap = new Sos_Model_ContactMapper();
        //Get all data of phone by phoneId
        $phoneDatas = array();
        $phoneDatas = $phoneMap->getAllPhoneDataByPid($phoneId);
        if (count($phoneDatas) > 0) {
            if ($phoneDatas['good_samaritan_status'] == 0) {
                $phoneDatas['good_samaritan_status'] = "OFF";
            } elseif ($phoneDatas['good_samaritan_status'] == 1) {
                $phoneDatas['good_samaritan_status'] = "ON";
            }
            
            $group = new Sos_Model_Contactgroup();
            $group->find($phoneDatas['alert_sendto_group']);
            
            if ($group->getId()) {
                $phoneDatas['alert_sendto_group'] = $group->getName();
            }

            if ($phoneDatas['incoming_government_alert'] == 0) {
                $phoneDatas['incoming_government_alert'] = "OFF";
            } elseif ($phoneDatas['incoming_government_alert'] == 1) {
                $phoneDatas['incoming_government_alert'] = "ON";
            }

            if ($phoneDatas['panic_alert_good_samaritan_status'] == 0) {
                $phoneDatas['panic_alert_good_samaritan_status'] = "OFF";
            } elseif ($phoneDatas['panic_alert_good_samaritan_status'] == 1) {
                $phoneDatas['panic_alert_good_samaritan_status'] = "ON";
            }
        }
        //Get all contact of phone by phoneId
        
        $contactGroups = new Sos_Model_Contactgroup();
        $contactGroupsMapper = new Sos_Model_ContactgroupMapper();
        $groups = $contactGroupsMapper->findByField('phone_id', $phoneId, $contactGroups);
        $contacts = array();
        if (count($groups)) {
            foreach ($groups as $g) {
                $contacts[] = $contactMap->getAllContactByPhoneId($phoneId, 0, $g->getId());
            }
        }
        //Assign data to view
        $this->view->phoneDatas = $phoneDatas;
        $this->view->contacts = $contacts;
        $this->view->groups = $groups;
}

    public function settingAction() {}

    public function listAction() {
        $phone = new Sos_Model_Phone();
        $phoneMapper = new Sos_Model_PhoneMapper();
        $auth = Sos_Service_Functions::webappAuth(false);
        if (!$auth->getId()) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/web/users/login/");
        } else {
            $phoneMapper->find($auth->getId(), $phone);
            if ($phone->getType() == 0)
                $phone->setType('Unknown');
            elseif ($phone->getType() == 1)
                $phone->setType('Iphone');
            elseif ($phone->getType() == 2)
                $phone->setType('Android');
            $this->view->phone = $phone;
        }
    }

    public function editAction() {
        return 0;
        $id = $this->_request->getParam("id");
        $txtName = $this->_request->getParam("txtName");
        $txtNumber = $this->_request->getParam("txtNumber");
        $txtImei = $this->_request->getParam("txtImei");
        $txtType = $this->_request->getParam("txtType");
        $hdType = $this->_request->getParam("hdType");

        $phone = new Sos_Model_Phone();
        $phoneMapper = new Sos_Model_PhoneMapper();

        if ($id == null) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/web/phone/list/");
        }

        $auth = Sos_Service_Functions::webappAuth(false);
        if (!$auth->getId()) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/web/users/login/");
        } else {
            $msg = null;
            $msg1 = null;
            $phoneMapper->findOneByField('id', $id, $phone);
            if ($hdType != NULL) {
                $phone->setName($txtName);
                //Check exist phone number when update
                if ($phone->getNumber() == $txtNumber || $this->phoneNumExist($txtNumber) == 0) {
                    $phone->setNumber($txtNumber);
                } else {
                    $msg1 = "Phone number: $txtNumber is exist.<br/>";
                }
                $phone->setImei($txtImei);
                $phone->setType($txtType);

                //Update phone information
                $phoneMapper->save($phone);
                $msg = $msg1;
                $msg .= "Update successfull !";
            }
            //Assign data to view
            $this->view->phoneRow = $phone;

            if ($msg != null) {
                $this->view->message = $msg;
            }
        }
    }

    public function addAction() {
       
    }

    public function deleteAction() {

    }

    public function activeAction() {
        $code = $this->_request->getParam('code');
        if ($code != null) {
            $this->view->code = $code;
        }
    }

    public function doactiveAction() {
        $code = $this->_request->getParam('code', '');
        $phone = new Sos_Model_Phone();
        $phoneMapper = new Sos_Model_PhoneMapper();
        $phoneMapper->findOneByField('token', $code, $phone);

        //If active code found
        $msg = null;
        if ($phone->getId() != NULL) {  
            if ($phone->getStatus() == 1) {
                $msg = "Your phone number " . $phone->getNumber() . " has already activated.";
            } else {
                // Check if phone will change number or imei from activate table (store temporary imei and number
                $activate = new Sos_Model_Activate();
                $activateMapper = new Sos_Model_ActivateMapper();
                $activateMapper->findOneByField('token', $code, $activate);
                if ($activate->getId() && $activate->getAction()) {
                    if ($activate->getNewImei()) {
                        $phone->setImei($activate->getNewImei());
                    }
                    if ($activate->getNewNumber()) {
                        $phone->setNumber($activate->getNewNumber());
                    }
                    $activate->setAction('0');
                    $activateMapper->save($activate);
                    Sos_Service_Functions::updateDefaultContact($phone);
                }
                //Save actived phone
                $phone->setStatus(1);
                $phoneMapper->save($phone);

                $msg = "CONGRATULATIONS !<br/> Your phone number " . $phone->getNumber();
                $msg .= " has been activated your website is ready for login at www.SOSbeacon.org";
            }
        }
        //If active code not found 
        else {
            $msg = "Your ACTIVATION CODE is not valid.";
        }

        $this->view->message = $msg;
    }

    public function smslogdeleteAction() {
        $id = $this->_request->getParam('id');

        $smslog = new Sos_Model_Smslog();
        $smslog->deleteRowByIds($id);

        //Back pre page
        $sess = new Zend_Session_Namespace('smslog');
        if (isset($sess->smslog))
            $this->_helper->getHelper('Redirector')->gotoUrl($sess->smslog);
    }

    public function smslogAction() {
        $box = $this->_request->getParam('box');
        $phone = new Sos_Model_Phone();
        $phoneMapper = new Sos_Model_PhoneMapper();
        $auth = Sos_Service_Functions::webappAuth(false);
        if (!$auth->getId()) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/web/users/login/");
        }
        $phoneMapper->find($auth->getId(), $phone);
        $smslog = $this->getSmslog($phone->getNumber(), $box);
        //Show Http Link in smslog message
        foreach ($smslog as $row) {
            $messageNew = Sos_Service_Functions::showHttpLink(htmlspecialchars($row->getMessage()));
            $row->setMessage($messageNew);
            //Re-write sent status of smsog
            if ($row->getStatus() == 0)
                $row->setStatus('Error');
            elseif ($row->getStatus() == 1)
                $row->setStatus('Sent');
        }
        $this->view->box = $box;
        $this->view->smslog = $smslog;
        //Save current uri
        $sess = new Zend_Session_Namespace('smslog');
        $sess->smslog = $this->getRequest()->getRequestUri();
    }

    //Check exist phone number
    private function phoneNumExist($phoneNum) {
        $phone = new Sos_Model_Phone();
        $phoneMap = new Sos_Model_PhoneMapper();
        $phoneMap->findOneByField('number', $phoneNum, $phone);
        if ($phone->getId() != NULL) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Get smslog : all phone, one phone, allbox, inbox, outbox
     * @param $userId
     * @param $number
     * @param $box
     */
    private function getSmslog($number, $box = null) {
        $arrSmslog = array();
        $phone = new Sos_Model_Phone();
        $phoneMapper = new Sos_Model_PhoneMapper();
        $where = NULL;
        //IF box = allbox
        if ($box == 0) {
            $where = "`from` = $number OR `to` = $number";
        }
        //IF box = inbox
        elseif ($box == 1) {
            $where = "`to` = $number";
        }
        //IF box = sent items
        elseif ($box == 2) {
            $where = "`from` = $number";
        }

        $smslog = new Sos_Model_Smslog();
        $smslogMapper = new Sos_Model_SmslogMapper();
        $arrSmslog = $smslogMapper->fetchList($where, 'id DESC');
        return $arrSmslog;
    }

}
