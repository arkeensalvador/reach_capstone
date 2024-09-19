<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenAI Chat Completion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>OpenAI Chat Completion</h1>
        
        <!-- Form for sending the prompt -->
        <form id="ai-form" method="POST" action="{{ route('ai.completion') }}">
            @csrf
            <div class="mb-3">
                <label for="prompt" class="form-label">Enter Prompt</label>
                <input type="text" class="form-control" id="prompt" name="prompt" placeholder="Ask something to OpenAI...">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <!-- Display result -->
        <div id="ai-result" class="mt-4">
            <h2>AI Response:</h2>
            <p id="response-content">No response yet.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.getElementById('ai-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            let formData = new FormData(this);
            let prompt = formData.get('prompt');

            axios.post("{{ route('ai.completion') }}", {
                prompt: prompt
            })
            .then(function (response) {
                document.getElementById('response-content').textContent = response.data.message;
            })
            .catch(function (error) {
                document.getElementById('response-content').textContent = 'Error: ' + error.response.data.message;
            });
        });
    </script>
</body>
</html>
