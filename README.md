# AI Balance Trainer 🎯

A Moodle plugin that teaches students how to collaborate effectively with AI while maintaining their independence and critical thinking skills.

## What is AI Balance Trainer?

AI Balance Trainer is an educational tool that implements a **5-level progression system** to help students learn balanced AI collaboration. Instead of becoming overly dependent on AI or avoiding it entirely, students learn to use AI as a strategic learning partner while developing their own problem-solving abilities.

## How It Works

### Learning Process
1. **Start**: New users begin at Tutor Mode with comprehensive AI guidance
2. **Advance**: System calculates independence ratio and advances students to higher levels
3. **Master**: Students learn to use AI strategically while maintaining independent thinking

## Features

### 📚 Multi-Subject Support
- **Programming**: Code challenges, debugging, algorithm design
- **Writing**: Essays, technical documentation, creative writing
- **Mathematics**: Problem solving, proofs, calculations
- **General**: Cross-disciplinary learning support

### 🏆 Challenge System
- Interactive subject-specific tasks with difficulty progression
- Self-assessment of AI dependency vs. independence


### 📊 Progress Tracking
- Real-time independence development across subjects
- Usage analytics and interaction patterns
- Performance metrics (AI-assisted vs. independent scores)

## Technical Details

### Core Components
- **`ai_balance_trainer.php`**: Main logic for level progression and AI interaction
- **`logger.php`**: Usage tracking and analytics
- **`api.php`**: RESTful API endpoints for frontend communication
- **`index.php`**: Modern, responsive web interface with Tailwind CSS

### AI Integration
- **Groq API**: Fast, reliable AI responses using Llama 3.1-8b-instant
- **Level-Specific Prompts**: Tailored AI behavior based on user level
- **Response Limiting**: Character limits ensure appropriate assistance levels

### Database
- **`local_groqchat_logs`**: Chat history and interaction tracking
- **`local_groqchat_user_progress`**: User progression and scores
- **`local_groqchat_challenges`**: Challenge completion tracking

## Configuration

- **API Settings**: Configure Groq API key and model selection
- **Training Parameters**: Adjust independence thresholds (20%, 40%, 60%, 80%)
- **Logging Options**: Set retention policies and logging preferences

## How to run:

1) Install Moodle server following the [instructions](https://download.moodle.org/) 

2) Branch `main` contains the main AI page on which the chat is displayed. The contents of this branch should be inserted into `pathToMoodle/server/moodle/local/aitrainer`

3) Branch `andrei` contains the the AI activity component which can be inserted on a course. The contents of this branch should be inserted into `pathToMoodle/server/moodle/mod/aiassistant`

4) Run the Moodle server following the instructions for your system from the official [guide](https://docs.moodle.org/500/en/Main_page)
