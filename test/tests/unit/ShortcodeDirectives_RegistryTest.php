<?php

/*
 $this->assertEquals()
$this->assertContains()
$this->assertFalse()
$this->assertTrue()
$this->assertNull()
$this->assertEmpty()
*/

class ShortcodeDirectives_RegistryTest extends \Codeception\Test\Unit {

    public function testGetPluginURL() {

    }

    public function testSetAdminNotice() {

    }

    public function testSetUp() {

        ShortcodeDirectives_Registry::$sDirPath = '';

        ShortcodeDirectives_Registry::setUp();
        $this->assertEquals(
            dirname( ShortcodeDirectives_Registry::$sFilePath ),
            ShortcodeDirectives_Registry::$sDirPath
        );

    }

    public function testReplyToShowAdminNotices() {

    }

    public function testRegisterClasses() {

        $_aClassFiles = $this->getStaticAttribute( 'ShortcodeDirectives_Registry', '___aAutoLoadClasses' );
        ShortcodeDirectives_Registry::registerClasses( $_aClassFiles );
        $this->assertAttributeEquals( $_aClassFiles , '___aAutoLoadClasses', 'ShortcodeDirectives_Registry' );

        $_aClassFiles = array( 'SomeClass' => 'SomeClass.php' );
        ShortcodeDirectives_Registry::registerClasses( $_aClassFiles );
        $this->assertAttributeNotEquals(
            $_aClassFiles ,
            '___aAutoLoadClasses',
            'ShortcodeDirectives_Registry'
        );

        $this->assertArrayHasKey(
            'SomeClass',
            $this->getStaticAttribute( 'ShortcodeDirectives_Registry', '___aAutoLoadClasses' ),
            'The key just set does not exist.'
        );

    }

    public function testReplyToLoadClass() {

        $this->assertFalse(
            class_exists( 'JustAClass' ),
            'The JustAClass class must not exist at this stage.'
        );
        include( codecept_root_dir() . '/tests/include/class-list.php' );
        ShortcodeDirectives_Registry::registerClasses( $_aClassFiles );
        $this->assertTrue(
            class_exists( 'JustAClass' ),
            'The class auto load failed with the ShortcodeDirectives_Registry::registerClasses() method.'
        );

    }

}
