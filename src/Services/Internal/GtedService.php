<?php
namespace App\Services\Internal;

class GtedService extends InternalService {

  public $ldapLinkId;
  public $roles;
  private $settings;

  public function __construct($logger, $settings) {
    parent::__construct($logger);
    $this->settings = $settings['gted'];
    $this->roles = $settings['gted']['roles'];
  }

  public function connect() {
    $this->setLdapLinkId();
    if (!$this->ldapLinkId) {
      return false;
    }
    return $this->ldapLogin();
  }

  private function setLdapLinkId() {
    $this->ldapLinkId = ldap_connect($this->settings['host'], $this->settings['port']);
  }

  private function ldapLogin() {
    return ldap_bind($this->ldapLinkId, $this->settings['connection_dn'], $this->settings['password']);
  }

  public function search($username) {
    if (!$this->ldapLinkId) {
      return false;
    }
    $filter = $this->settings['search_dn'][0] . $username;
    $resultId = ldap_search($this->ldapLinkId, $this->settings['search_dn'][1], $filter);
    if (!$resultId) {
      return false;
    }
    $entries = ldap_get_entries($this->ldapLinkId, $resultId);
    $this->logger()->info('GtedSvc:search - entries', [$entries]);
    return $entries[0];
  }

  public function extractUserData($data) {
    $userData = array(
      'username' => $data['gtprimarygtaccountusername'][0],
      'gtID' => $data['gtgtid'][0],
      'name' => $data['displayname'][0],
      'email' => $data['gtprimaryemailaddress'][0],
    );
    if ($data['edupersonaffiliation'][0] == 'staff' || $data['edupersonaffiliation'][1] == 'staff') {
      $userData['roleID'] = $this->roles['staff'];
    }
    if ($data['edupersonaffiliation'][0] == 'student' || $data['edupersonaffiliation'][1] == 'student') {
      $userData['roleID'] = $this->roles['student'];
    }
    $this->logger()->info('extracted user data', array('userData' => $userData));
    return $userData;
  }

  public function disconnect() {
    if ($this->ldapLinkId) ldap_unbind($this->ldapLinkId);
  }


}
