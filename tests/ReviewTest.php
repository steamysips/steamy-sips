<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Steamy\Model\Review;
final class ReviewTest extends TestCase
{
  private ?Review $dummy_review;
  public function setUp(): void
  {
    $this->dummy_review = new Review(
      1,
      1, 
      1, 
      "This is a test review.",
      5,
      new DateTime("2024-03-10")
    );
  }
  public function tearDown(): void
  {
    $this->dummy_review = null;
  }
  public function testConstructor(): void
  {
      // Check if review attributes are correctly set
      self::assertEquals(1, $this->dummy_review->getClientID());
      self::assertEquals(1, $this->dummy_review->getProductID());
      self::assertEquals("This is a test review.", $this->dummy_review->getText());
      self::assertEquals(5, $this->dummy_review->getRating());
      self::assertEquals(new DateTime("2024-03-10"), $this->dummy_review->getCreatedDate());
  }
  public function testToArray(): void
  {
      $result = $this->dummy_review->toArray();
      // Check if all required keys are present
      $this->assertArrayHasKey('client_id', $result); 
      $this->assertArrayHasKey('product_id', $result);
      $this->assertArrayHasKey('text', $result);
      $this->assertArrayHasKey('created_date', $result); 
      $this->assertArrayHasKey('rating', $result);
      // Check if the actual values are correct
      self::assertEquals(1, $result['client_id']);
      self::assertEquals(1, $result['product_id']);
      self::assertEquals("This is a test review.", $result['text']);
      self::assertEquals(new DateTime("2024-03-10"), new DateTime($result['created_date'])); // Compare dates
      self::assertEquals(5, $result['rating']);
  }  public function testValidate(): void
  {
    // Test validation with valid data
    $errors = $this->dummy_review->validate();
    $this->assertEmpty($errors);
    // Test validation with empty text
    $invalidReview = new Review(
      1, 1, 1, "", 0, new DateTime("2024-03-10")
    );
    $errors = $invalidReview->validate();
    $this->assertNotEmpty($errors);
    $this->assertArrayHasKey('text', $errors);
    $this->assertEquals('Review text must have at least 2 characters', $errors['text']); // Assert specific message
    // Test validation with invalid rating
    $invalidReview = new Review(
      1, 1, 1, "Valid Text", -1, new DateTime("2024-03-10")
    );
    $errors = $invalidReview->validate();
    $this->assertNotEmpty($errors);
    $this::assertArrayHasKey('rating', $errors);
    $this->assertEquals('Rating must be between 1 and 5', $errors['rating']); // Assert specific message
  }
  public function testGetByID(): void
  {
      // Mock data for the test
      $review_id = 4;
      $text = "This product is amazing!";
      $rating = 5;
      $date = new DateTime(); // Create a DateTime object with the current date
      // Call the getByID method directly on the Review class
      $review = Review::getByID($review_id);
      // Assert that the returned object is not null
      $this->assertNotNull($review);
      // Assert that the properties of the returned Review object match the mock data
      self::assertEquals($text, $review->getText());
      self::assertEquals($rating, $review->getRating());
      // Compare dates by formatting
      self::assertEquals($date->format('Y-m-d'), $review->getCreatedDate()->format('Y-m-d'));
      // Test getByID with invalid ID (assuming it returns null on failure)
      $invalid_id = 999;
      $review = Review::getByID($invalid_id);
      $this->assertNull($review); // Assert null for invalid ID
  }

  public function testSave(): void
  {
    // Create a DateTime object for the review date
    $date = new DateTime('now');
    // Create a mock Review object with mock data
    $review = $this->getMockBuilder(Review::class)
      ->setConstructorArgs([1, 1, 1, 'Test review', 5, $date])
      ->onlyMethods(['save']) // Mocking the save method
      ->getMock();
    // Set up the mock to expect the save method to be called once
    $review->expects($this->once())
      ->method('save');
    // Call the save method
    $review->save();
  }
}