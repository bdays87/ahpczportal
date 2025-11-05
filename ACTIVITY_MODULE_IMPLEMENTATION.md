# Professional Development Activity Module

## Overview
A comprehensive module that allows administrators to create professional development activities linked to professions, enabling customers to enroll, participate, and take quizzes to earn points based on their performance.

## 🗄️ Database Structure

### Core Tables Created:
1. **`activities`** - Stores activity information (title, description, type, content, points)
2. **`activity_professions`** - Links activities to professions (many-to-many)
3. **`activity_enrollments`** - Tracks customer enrollments and progress
4. **`activity_quizzes`** - Quiz configuration for activities
5. **`quiz_questions`** - Questions within quizzes
6. **`quiz_answers`** - Answer options for questions
7. **`customer_quiz_attempts`** - Tracks customer quiz attempts and scores

## 🎯 Key Features

### For Administrators:
- **Activity Management**: Create, edit, delete activities
- **Multi-format Support**: Videos, articles, and file attachments
- **Profession Linking**: Associate activities with one or more professions
- **Quiz Builder**: Create quizzes with multiple question types
- **Points System**: Assign points to activities and questions
- **Progress Tracking**: Monitor enrollments and completion rates

### For Customers:
- **Activity Discovery**: View activities relevant to their professions
- **Content Consumption**: Watch videos, read articles, download attachments
- **Quiz Taking**: Complete quizzes to earn points
- **Progress Tracking**: Monitor their learning journey
- **Points Accumulation**: Earn points based on quiz performance

## 📊 Points Calculation System

### How Points Are Calculated:
1. **Activity Points**: Base points assigned to each activity
2. **Quiz Questions**: Individual points per question (1-100 points)
3. **Performance Based**: Points earned = (Correct Answers / Total Questions) × Activity Points
4. **Pass Threshold**: Configurable pass percentage (default: 70%)
5. **Multiple Attempts**: Best score counts, with attempt limits

### Example Calculation:
```
Activity: 50 points
Quiz: 10 questions (5 points each)
Customer Score: 8/10 correct (80%)
Points Earned: 8 × 5 = 40 points
Status: PASSED (80% > 70% threshold)
```

## 🔧 Technical Implementation

### Models Created:
- **Activity**: Main activity model with relationships
- **ActivityEnrollment**: Customer enrollment tracking
- **ActivityQuiz**: Quiz configuration
- **QuizQuestion**: Question management
- **QuizAnswer**: Answer options
- **CustomerQuizAttempt**: Attempt tracking and scoring

### Repositories:
- **ActivityRepository**: Activity CRUD and enrollment management
- **QuizRepository**: Quiz management and attempt processing

### Livewire Components:
- **Admin/Activities**: Activity management interface
- **Admin/ActivityQuizManagement**: Quiz builder
- **Customer/Activities**: Customer activity browser
- **Customer/ActivityView**: Individual activity viewer
- **Customer/QuizTaking**: Quiz taking interface

## 🚀 Usage Workflow

### Administrator Workflow:
1. **Create Activity**: Set title, description, type (video/article/attachment)
2. **Set Content**: Add video URL, article text, or upload files
3. **Link Professions**: Associate with relevant professions
4. **Create Quiz**: Add questions and answers
5. **Assign Points**: Set activity and question points
6. **Publish**: Make available to customers

### Customer Workflow:
1. **Browse Activities**: View activities for their professions
2. **Enroll**: Join activities of interest
3. **Consume Content**: Watch/read/download activity content
4. **Take Quiz**: Complete quiz after content consumption
5. **Earn Points**: Receive points based on performance
6. **Track Progress**: Monitor learning journey

## 📈 Advanced Features

### Quiz Types Supported:
- **Multiple Choice**: Select multiple correct answers
- **Single Choice**: Select one correct answer
- **True/False**: Binary choice questions

### Enrollment States:
- **ENROLLED**: Customer registered for activity
- **IN_PROGRESS**: Customer started the activity
- **COMPLETED**: Customer finished and passed quiz
- **DROPPED**: Customer abandoned the activity

### Activity States:
- **DRAFT**: Under development, not visible to customers
- **PUBLISHED**: Live and available for enrollment
- **ARCHIVED**: No longer available for new enrollments

## 🔐 Security & Validation

### Access Control:
- Customers can only see activities for their professions
- Quiz attempts are limited (configurable per quiz)
- File uploads are validated and secured
- All forms include comprehensive validation

### Data Integrity:
- Foreign key constraints ensure data consistency
- Soft deletes prevent data loss
- Audit trails track creation and modifications
- Points calculation includes fraud prevention

## 📱 User Interface

### Admin Interface:
- **Dashboard View**: Activity statistics and management
- **Form Builder**: Intuitive activity creation
- **Quiz Builder**: Drag-and-drop question management
- **Analytics**: Enrollment and completion tracking

### Customer Interface:
- **Activity Browser**: Grid/list view of available activities
- **Content Player**: Embedded video player, article reader
- **Quiz Interface**: Clean, distraction-free quiz taking
- **Progress Dashboard**: Personal learning analytics

## 🔄 Integration Points

### Existing System Integration:
- **Professions**: Links to existing profession system
- **Customers**: Uses customer authentication and profiles
- **Points System**: Can integrate with CDP/continuing education
- **Notifications**: Activity completion notifications
- **Reporting**: Analytics and progress reports

## 🎨 Responsive Design
- Mobile-first approach for all interfaces
- Touch-friendly quiz taking experience
- Responsive video player and content viewer
- Accessible design following WCAG guidelines

## 📊 Analytics & Reporting

### Available Metrics:
- Activity enrollment rates
- Completion percentages
- Average quiz scores
- Time spent on activities
- Popular content analysis
- Customer engagement tracking

This comprehensive module provides a complete professional development platform that can scale to support thousands of activities and users while maintaining high performance and user experience standards.

## 🚀 Next Steps
1. Add the admin route to web.php
2. Create customer routes for activity access
3. Implement notification system for completions
4. Add reporting dashboard
5. Create mobile app integration APIs
