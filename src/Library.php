<?php
    class Library
    {
        private $id;
        private $name;

        function __construct($name, $id = null)
        {
            $this->name = $name;
            $this->id = $id;
        }

        function getName()
        {
            return $this->name;
        }

        function setName($new_name)
        {
            $this->name = (string) $new_name;
        }

        function getCheckedOut()
        {
            return $this->checked_out;
        }

        function setCheckedOut($checked_out)
        {
            $this->checked_out = $checked_out;
        }

        function getBooksAvailable()
        {
            return $this->books_available;
        }

        function setBooksAvailable($books_available)
        {
            $this->books_available = $books_available;
        }

        function getId()
        {
            return $this->id;
        }

        function setId($id)
        {
            $this->id = $id;
        }

        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO libraries (name) VALUES ('{$this->getName()}');");

            $query = $GLOBALS['DB']->query("SELECT id FROM libraries WHERE name = '{$this->getName()}';");
            $rs = $query->fetchAll(PDO::FETCH_ASSOC);
            $this->id = $rs[0]['id'];
        }

        static function getAll()
        {
            $all_libraries = array();
            $query = $GLOBALS['DB']->query("SELECT * FROM libraries;");
            foreach ($query as $library) {
                $name = $library['name'];
                $id = $library['id'];
                $new_library = new Library($name, $id);
                array_push($all_libraries, $new_library);
            }
            return $all_libraries;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM libraries;");
        }

        //add book object to library_books
        function addBook($new_book)
        {
            $library_id = $this->getId();
            $book_id = $new_book->getId();

            $GLOBALS['DB']->exec("INSERT library_books (library_id, book_id) VALUES ({$library_id}, {$book_id});");

        }

        function getLibraryBooks()
        {
            $books_query = $GLOBALS['DB']->query("SELECT book_id FROM library_books WHERE library_id = {$this->getId()};");
            $library_books = array();
            foreach($books_query as $book_id) {
                $new_book = Book::find($book_id['book_id']);
                array_push($library_books, $new_book);
            }

            function cmp($a, $b)
            {
                return strcmp($a->getTitle(), $b->getTitle());
            }

            usort($library_books, "cmp");

            return $library_books;
        }

        function getLibraryBooksAvailable()
        {


            $books_query = $GLOBALS['DB']->query("SELECT book_id FROM library_books WHERE library_id = {$this->getId()} AND status IS null;");
            $library_books = array();
            foreach($books_query as $book_id) {
                $new_book = Book::find($book_id['book_id']);
                array_push($library_books, $new_book);
            }
            function cmp($a, $b)
            {
                return strcmp($a->getTitle(), $b->getTitle());
            }

            usort($library_books, "cmp");
            return $library_books;
        }

        function deleteBook($book_object)
        {
            $GLOBALS['DB']->exec("DELETE FROM library_books WHERE book_id = {$book_object->getId()};");
        }

        function addPatron($new_patron)
        {
            $GLOBALS['DB']->exec("INSERT INTO libraries_patrons (library_id, patron_id) VALUES ({$this->getId()}, {$new_patron->getId()});");

        }

        function getLibraryPatrons()
        {
            //get list of patron ids by library
            $returned_patron_ids = $GLOBALS['DB']->query("SELECT patron_id FROM libraries_patrons WHERE library_id = {$this->getId()};");

            $library_patrons = array();
            foreach($returned_patron_ids as $id) {
                $patron_id = $id['patron_id'];
                $new_patron = Patron::find($patron_id);
                array_push($library_patrons, $new_patron);
            }
            return $library_patrons;
        }

        function deletePatron($patron)
        {
            $GLOBALS['DB']->exec("DELETE FROM libraries_patrons WHERE patron_id = {$patron->getId()};");
        }

        function checkout($book, $patron)
        {
            $GLOBALS['DB']->exec("UPDATE library_books SET status ='out', checkout_patron_id = {$patron->getId()}, checkout_date = NOW(), due_date = NOW() + INTERVAL 10 DAY WHERE book_id = {$book->getId()} AND library_id = {$this->getId()} LIMIT 1;");
        }

        function returnBook($book, $patron)
        {
            $GLOBALS['DB']->exec("UPDATE library_books SET status =null, checkout_patron_id = null, checkout_date = null, due_date = null WHERE book_id = {$book->getId()} AND checkout_patron_id = {$patron->getId()} AND library_id = {$this->getId()} LIMIT 1;");
        }

        static function find($search_id)
        {
            $found_library = null;
            $query = $GLOBALS['DB']->query("SELECT * FROM libraries where id = {$search_id};");
            $rs = $query->fetchAll(PDO::FETCH_ASSOC);
            $name = $rs[0]['name'];
            if ($name) {
                $found_library= new Library($name, $search_id);
            }
            return $found_library;
        }

        function getOverdueBooks()
        {
            $overdue_titles = array();
            $query = $GLOBALS['DB']->query("SELECT books_descriptions.title FROM library_books JOIN books_authors ON (books_authors.id = library_books.book_id) JOIN books_descriptions ON (books_descriptions.id = books_authors.book_description_id) WHERE library_books.due_date < NOW();");
            foreach($query as $book_title) {
                $title = $book_title['title'];
                array_push($overdue_titles, $title);
            }
            return $overdue_titles;
        }


    }

?>
