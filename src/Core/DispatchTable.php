<?php

namespace Rawr\Core;

final class DispatchTable {
  private static $table = [];

  static function tableExists($table) {
    return array_key_exists($table, static::$table);
  }

  static function createTable($table) {
    if (!static::tableExists($table)) {
      static::$table[$table] = [];
    } else {
      throw new \Exception("[rawr-core] Table [{$table}] already exists");
    }
  }

  static function clearTable($table) {
    if (static::tableExists($table)) {
      static::$table[$table] = [];
    } else {
      throw new \Exception("[rawr-core] Unable to clear inexistent table [{$table}]");
    }
  }

  static function deleteTable($table) {
    if (static::tableExists($table)) {
      unset(static::$table[$table]);
    } else {
      throw new \Exception("[rawr-core] Unable to delete inexistent table [{$table}]");
    }
  }

  static function tableHasKey($table, $key) {
    return static::tableExists($table) && array_key_exists($key, static::$table[$table]);
  }

  static function addToTable($table, $key, $value) {
    if (static::tableExists($table)) {
      if (!static::tableHasKey($table, $key)) {
        static::$table[$table][$key] = $value;
      } else {
        throw new \Exception("[rawr-core] Duplicated key [{$key}] on table [{$table}]");
      }
    } else {
      throw new \Exception("[rawr-core] Inexistent table [{$table}]");
    }
  }

  static function getFromTable($table, $key) {
    // TODO
  }

  static function removeFromTable($table, $key) {
    // TODO
  }

  static function debugTable($table) {
    if (array_key_exists($table, static::$table)) {
      var_dump(static::$table[$table]);
    } else {
      throw new \Exception("[rawr-core] Table [{$table}] not found");
    }
  }

  static function __GET__() {
    return static::$table;
  }
}
