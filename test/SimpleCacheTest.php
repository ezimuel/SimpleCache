<?php
/**
 * Unit test for SimpleCache
 * 
 * @author Enrico Zimuel (enrico@zimuel.it)
 * @copyright Copyright (C) 2011 Enrico Zimuel - http://www.zimuel.it
 * @license GNU General Public License - http://www.gnu.org/licenses/gpl.html
 */
require_once '../SimpleCache.php';

class SimpleCacheTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->cache= new SimpleCache('/tmp');
    }
    public function testConstants()
    {
        $this->assertEquals(SimpleCache::DEFAULT_LIFETIME,60);
    }
    public function testSave()
    {
        $result= $this->cache->save('This is a test','test');
        $this->assertTrue($result);
        $filename= '/tmp/.simplecache_'.md5('test').'.php';
        $this->assertTrue(file_exists($filename));
    }
    public function testLoad()
    {
        $result= $this->cache->load('test');
        $this->assertEquals($result,'This is a test');
    }
    public function testRemove()
    {
        $result= $this->cache->remove('test');
        $this->assertTrue($result);
        $filename= '/tmp/.simplecache_'.md5('test').'.php';
        $this->assertTrue(file_exists($filename)===false);
    }
    public function testClean()
    {
        $result= $this->cache->save('This is a test','test');
        $this->assertTrue($result);
        $result= $this->cache->clean();
        $filename= '/tmp/.simplecache_'.md5('test').'.php';
        $this->assertTrue(file_exists($filename)===false);
    }
}
