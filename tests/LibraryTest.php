<?php
    /**
    * @backupGlobals disabled
    * #backupStaticAttributes disabled
    */

    require_once "src/Book.php";
    require_once "src/Library.php";
    require_once "src/Patron.php";

    $server = 'mysql:host=localhost:8889;dbname=oracle_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class LibraryTest extends PHPUnit_Framework_TestCase
    {

        protected function tearDown()
        {
            Library::deleteAll();
            Book::deleteAll();
            Patron::deleteAll();
        }

        function test_saveAndGetAll()
        {
            $name = "Portland Library";
            $new_library = new Library($name);
            $new_library->save();

            $result = Library::getAll();

            $this->assertEquals([$new_library], $result);
        }

        function test_addAndGetBooks()
        {
            $name = "Portland Library";
            $new_library = new Library($name);
            $new_library->save();

            $title = "A Tale of Two Cities";
            $first_name = "Charles";
            $last_name = "Dickens";
            $full_name = $first_name . " " . $last_name;
            $authors = array($full_name => array('first_name' => $first_name, 'last_name' => $last_name));
            $summary = "A story about the French revolution";
            $category = "fiction";

            $new_book = new Book($title, $authors, $summary, $category);
            $new_book->save();

            $new_library->addBook($new_book);

            $result = $new_library->getLibraryBooks();

            $this->assertEquals([$new_book], $result);
        }

        function test_removeBookFromLibrary()
        {
            $name = "Portland Library";
            $new_library = new Library($name);
            $new_library->save();

            $title = "A Tale of Two Cities";
            $first_name = "Charles";
            $last_name = "Dickens";
            $full_name = $first_name . " " . $last_name;
            $authors = array($full_name => array('first_name' => $first_name, 'last_name' => $last_name));
            $summary = "A story about the French revolution";
            $category = "fiction";
            $new_book = new Book($title, $authors, $summary, $category);
            $new_book->save();

            $title = "A Tale of Another City";
            $first_name = "Charles";
            $last_name = "Dickens";
            $full_name = $first_name . " " . $last_name;
            $authors2 = array($full_name => array('first_name' => $first_name, 'last_name' => $last_name));
            $summary = "A story about something";
            $category = "fiction";
            $new_book2 = new Book($title, $authors2, $summary, $category);
            $new_book2->save();

            $new_library->addBook($new_book);
            $new_library->addBook($new_book2);

            $new_library->deleteBook($new_book2);
            $result = $new_library->getLibraryBooks();

            $this->assertEquals([$new_book], $result);
        }

        function test_addAndGetPatrons()
        {
            $name = "Portland Library";
            $new_library = new Library($name);
            $new_library->save();

            $first_name = "Bob";
            $last_name = "Smith";
            $new_patron = new Patron($first_name, $last_name);
            $new_patron->save();

            $first_name = "Jane";
            $last_name = "Doe";
            $new_patron2 = new Patron($first_name, $last_name);
            $new_patron2->save();

            $new_library->addPatron($new_patron);
            $new_library->addPatron($new_patron2);

            $result = $new_library->getLibraryPatrons();

            $this->assertEquals([$new_patron, $new_patron2], $result);
        }

        function test_deletePatron()
        {
            $name = "Portland Library";
            $new_library = new Library($name);
            $new_library->save();

            $first_name = "Bob";
            $last_name = "Smith";
            $new_patron = new Patron($first_name, $last_name);
            $new_patron->save();

            $first_name = "Jane";
            $last_name = "Doe";
            $new_patron2 = new Patron($first_name, $last_name);
            $new_patron2->save();

            $new_library->addPatron($new_patron);
            $new_library->addPatron($new_patron2);

            $new_library->deletePatron($new_patron);
            $result = $new_library->getLibraryPatrons();

            $this->assertEquals([$new_patron2], $result);
        }

        function test_checkoutBooks()
        {
            $name = "Portland Library";
            $new_library = new Library($name);
            $new_library->save();

            $first_name = "Bob";
            $last_name = "Smith";
            $new_patron = new Patron($first_name, $last_name);
            $new_patron->save();

            $first_name = "Jane";
            $last_name = "Doe";
            $new_patron2 = new Patron($first_name, $last_name);
            $new_patron2->save();

            $new_library->addPatron($new_patron);
            $new_library->addPatron($new_patron2);

            $title = "A Tale of Two Cities";
            $first_name = "Charles";
            $last_name = "Dickens";
            $full_name = $first_name . " " . $last_name;
            $authors = array($full_name => array('first_name' => $first_name, 'last_name' => $last_name));
            $summary = "A story about the French revolution";
            $category = "fiction";

            $new_book = new Book($title, $authors, $summary, $category);
            $new_book->save();

            $title = "A Tale of Another City";
            $first_name = "Charles";
            $last_name = "Dickens";
            $full_name = $first_name . " " . $last_name;
            $authors2 = array($full_name => array('first_name' => $first_name, 'last_name' => $last_name));
            $summary = "A story about something";
            $category = "fiction";

            $new_book2 = new Book($title, $authors2, $summary, $category);
            $new_book2->save();

            $new_library->addBook($new_book);
            $new_library->addBook($new_book2);

            $new_library->checkout($new_book, $new_patron2);
            $result = $new_patron2->getCheckedoutBooks($new_library);

            $this->assertEquals([$new_book], $result);
        }

        function test_returnBook()
        {
            $name = "Portland Library";
            $new_library = new Library($name);
            $new_library->save();

            $first_name = "Bob";
            $last_name = "Smith";
            $new_patron = new Patron($first_name, $last_name);
            $new_patron->save();

            $new_library->addPatron($new_patron);

            $title = "A Tale of Two Cities";
            $first_name = "Charles";
            $last_name = "Dickens";
            $full_name = $first_name . " " . $last_name;
            $authors = array($full_name => array('first_name' => $first_name, 'last_name' => $last_name));
            $summary = "A story about the French revolution";
            $category = "fiction";
            $new_book = new Book($title, $authors, $summary, $category);
            $new_book->save();

            $title = "A Tale of Another City";
            $first_name = "Charles";
            $last_name = "Dickens";
            $full_name = $first_name . " " . $last_name;
            $authors2 = array($full_name => array('first_name' => $first_name, 'last_name' => $last_name));
            $summary = "A story about something";
            $category = "fiction";
            $new_book2 = new Book($title, $authors2, $summary, $category);
            $new_book2->save();

            $new_library->addBook($new_book);
            $new_library->addBook($new_book2);

            $new_library->checkout($new_book, $new_patron);
            $new_library->checkout($new_book2, $new_patron);

            $new_library->returnBook($new_book2, $new_patron);

            $result = $new_patron->getCheckedoutBooks($new_library);

            $this->assertEquals([$new_book], $result);
        }

        




    }

?>
