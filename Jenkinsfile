pipeline {
     agent any
     stages {
        stage("Deploy") {
            steps {
                sh "rm -rf /usr/share/nginx/vfulfill/development/server_automations/*"
                sh "cp -r ${WORKSPACE}/* /usr/share/nginx/vfulfill/development/server_automations/"
            }
        }
    }
}
