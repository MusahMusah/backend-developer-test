# Back-end Developer Test

This is my solution to the provided Scenarios in the assessment

## Installation
### Local installation
- Installation with composer:
    - Run `composer install` to install the dependencies.
    - Create a `.env` file using the [.env.example](/.env.example) file as a template. All the appropriate values has been filled in the `.env.example`, this will enable you to run the application locally without issues.
    - Run `php artisan key:generate` to generate the application key.
    - Run `php artisan migrate --seed` to run all migrations and seeders

## My Solution
### 1. Achievement Storage and Enum
   I chose to create a dedicated table to store achievements in the system, differentiating them by a `"type"` column defined as an enum class **(AchievementTypeEnum)**. This design decision allows the achievements to be extendable easily. By using an enum, the system becomes scalable, making it straightforward to add new achievement types in the future. This flexibility ensures that the system can accommodate various types of achievements without major modifications. Additionally, I implemented a seeder to provide a convenient way to seed the database with default achievements related to comments watched and lessons watched, maintaining persistent data.

### 2. Badge Storage
   Similarly, I created a dedicated table to store all badges provided in the assessment. This approach enhances efficiency and manageability. Storing badges in a separate table makes it easy to manage existing badges and add new ones as needed. The system is designed to be extensible, enabling seamless addition of new badges without complex modifications. I implemented a seeder to populate the database with default badges provided in the assessment, ensuring a consistent starting point for badge data.

### 3. Achievement Service
   I introduced an Achievement Service class to encapsulate the core business logic related to creating achievements for users and dispatching the achievement unlock event. This separation of concerns enhances code organization and maintainability. The Achievement Service class can be reused in various parts of the system, such as in the AchievementUnlockedListener and LessonWatchedListener. This modular approach promotes code reuse and allows for easier unit testing of the service logic.

## Oversight
  My solution on the lessons was based on the lessons that belongs to a user not lessons watched by a user. The test cases also have this oversight.

## Future Improvements
### 1. Badges with Levels
   Extend the badge system to include multiple levels for each badge. Users could progress through different levels based on their achievements, providing a gamified experience and additional motivation for user engagement.
### 2. User Notifications
   Implement a notification system to inform users when they unlock a new achievement or badge. This enhances the user experience and keeps users engaged with the achievement system.
## Testing 🚨
- Automated testing
    - Run `php artisan test` to execute the test cases.

