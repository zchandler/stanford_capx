<?php
/**
 * @file
 * @author [author] <[email]>
 */

namespace CAPx\Drupal\Util;

class CAPx {

  // The last request CAP server request response.
  public static $data;

  /**
   * Returns a fully loaded entity from the DB
   * @param  [type] $profileId [description]
   * @return [type]            [description]
   */
  public static function getEntityByProfileId($entityType, $bundleType, $profileId) {
    // @TODO: CACHE THIS!

    $entityId = CAPx::getEntityIdByProfileId($entityType, $bundleType, $profileId);

    if (!$entityId) {
      return FALSE;
    }

    $entity =  entity_load_single($entityType, $entityId);
    return $entity;

  }

  /**
   * [getEntityIdByProfileId description]
   * @param  [type] $entityType [description]
   * @param  [type] $bundleType [description]
   * @param  [type] $profileId  [description]
   * @return [type]             [description]
   */
  public static function getEntityIdByProfileId($entityType, $bundleType, $profileId) {

    $query = db_select("capx_profiles", 'capx')
      ->fields('capx', array('entity_id'))
      ->condition('entity_type', $entityType)
      ->condition('bundle_type', $bundleType)
      ->condition('profile_id', $profileId)
      ->execute()
      ->fetchAssoc();

    return isset($query['entity_id']) ? $query['entity_id'] : FALSE;

  }

  /**
   * [setData description]
   * @param [type] $data [description]
   */
  public static function setData($data) {
    CAPx::$data = $data;
  }

  /**
   * [getData description]
   * @return [type] [description]
   */
  public static function getData() {
    return CAPx::$data;
  }

  /**
   * [insertNewProfileRecord description]
   * @param  [type] $entity [description]
   * @return [type]         [description]
   */
  public static function insertNewProfileRecord($entity, $profileId, $etag = '') {
    $id = $entity->getIdentifier();
    $entityType = $entity->type();
    $bundleType = $entity->getBundle();

    $record = array(
      'entity_type' => $entityType,
      'entity_id' => $id,
      'importer' => '',
      'profile_id' => $profileId,
      'etag' => $etag,
      'bundle_type' => $bundleType,
      'sync' => 1,
    );

    $yes = drupal_write_record('capx_profiles', $record);

    if (!$yes) {
      watchdog('CAPx', 'Could not insert record for capx_profiles on profile id: ' . $profileId, array(), WATCHDOG_ERROR);
    }
  }

}
