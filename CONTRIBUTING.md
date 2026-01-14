# Contributing Guide

## Development Workflow

### 1. Branch Strategy (Git Flow)

```
main (production)
  ├── develop (integration)
  │   ├── feature/bofa-rag-demo
  │   ├── feature/chatbot-ui
  │   └── hotfix/security-patch
```

**Branch Naming:**
- `feature/` - New features
- `bugfix/` - Bug fixes
- `hotfix/` - Urgent production fixes
- `chore/` - Maintenance tasks

### 2. Making Changes

```bash
# 1. Create feature branch from develop
git checkout develop
git pull origin develop
git checkout -b feature/your-feature-name

# 2. Make changes, commit frequently
git add .
git commit -m "feat: add RAG evaluation metrics"

# 3. Push to remote
git push origin feature/your-feature-name

# 4. Create Pull Request on GitHub
# Title: "Add RAG evaluation metrics with DeepEval"
# Description: What changed, why, testing done
```

### 3. Commit Message Convention

Follow **Conventional Commits** (standard at BofA/enterprise):

```
<type>(<scope>): <subject>

<body>

<footer>
```

**Types:**
- `feat:` - New feature
- `fix:` - Bug fix
- `docs:` - Documentation only
- `style:` - Code style (formatting, no logic change)
- `refactor:` - Code restructuring
- `test:` - Adding tests
- `chore:` - Maintenance

**Examples:**
```bash
git commit -m "feat(rag): add LLM Guard guardrails for PII detection"
git commit -m "fix(api): handle empty query in FastAPI endpoint"
git commit -m "docs(readme): add deployment instructions for GoDaddy"
git commit -m "test(evaluation): add adversarial test cases for bias probes"
```

### 4. Pull Request Checklist

Before creating PR, ensure:

- [ ] Code passes linting (`pylint`, `flake8`)
- [ ] No security vulnerabilities (`bandit`, `safety check`)
- [ ] Tests pass (if applicable)
- [ ] Documentation updated (README, docstrings)
- [ ] No secrets/credentials committed (`.env` in `.gitignore`)
- [ ] Branch is up to date with `develop`

### 5. Code Review Process

**At BofA, PRs require:**
1. ✅ 2+ approvals from senior engineers
2. ✅ All CI checks passing (linting, tests, security scans)
3. ✅ No merge conflicts
4. ✅ Change Advisory Board (CAB) approval for production

**Review criteria:**
- Code quality (readable, maintainable)
- Performance implications
- Security considerations
- Test coverage
- Documentation completeness

### 6. Merging Strategy

**For this project:**
```bash
# Squash merge feature branches into develop
git checkout develop
git merge --squash feature/your-feature-name
git commit -m "feat: your feature summary"

# Merge develop into main for releases
git checkout main
git merge develop --no-ff
git tag -a v1.0.0 -m "Release v1.0.0: BofA RAG Demo"
git push origin main --tags
```

### 7. CI/CD Pipeline

**Automated on every push:**
1. **Code Quality** - Pylint, Flake8
2. **Security Scan** - Bandit (SAST), Safety (dependency check)
3. **Tests** - pytest with coverage report
4. **Deploy** (main branch only) - Deploy to brownbi.com

### 8. Local Development Setup

```bash
# 1. Clone repo
git clone https://github.com/mtnjxynt6p-ai/brownbi_com.git
cd brownbi_com

# 2. Create virtual environment
python3 -m venv venv
source venv/bin/activate

# 3. Install dependencies
cd BofA-Compliant-Finance-RAG-Demo
pip install -r requirements.txt

# 4. Set up pre-commit hooks (optional but recommended)
pip install pre-commit
pre-commit install

# 5. Copy environment template
cp .env.example .env
# Edit .env with your credentials

# 6. Run locally
python app.py
```

### 9. Pre-commit Hooks (Recommended)

Create `.pre-commit-config.yaml`:

```yaml
repos:
  - repo: https://github.com/pre-commit/pre-commit-hooks
    rev: v4.5.0
    hooks:
      - id: trailing-whitespace
      - id: end-of-file-fixer
      - id: check-yaml
      - id: check-added-large-files
      - id: detect-private-key
  
  - repo: https://github.com/psf/black
    rev: 23.12.1
    hooks:
      - id: black
  
  - repo: https://github.com/PyCQA/flake8
    rev: 7.0.0
    hooks:
      - id: flake8
```

Install: `pre-commit install`

Now every commit is auto-checked for code quality!

### 10. Deployment Process

**Development:**
```bash
git push origin develop
# Auto-deploys to staging.brownbi.com
```

**Production:**
```bash
git checkout main
git merge develop
git tag v1.0.0
git push origin main --tags
# Auto-deploys to brownbi.com
# Creates GitHub release
```

## Questions?

Contact: Russell Brown @ brownbi.com
