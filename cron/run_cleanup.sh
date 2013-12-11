#!/bin/bash
#
# Copyright 2013 MTU Aero Engines AG
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#   http://www.apache.org/licenses/LICENSE-2.0
#
# Unless requir ed by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

# Secret key from Mantis installation
# You can set it in the environment:
# export MANTIS_CLEANUP_SECRET_KEY="XXXXXX"
# or at run time:
# MANTIS_CLEANUP_SECRET_KEY="XXXXXX" ./run_cleanup.sh
SECRET_KEY=${MANTIS_CLEANUP_SECRET_KEY:?}

# Mantis installation base URL, set it as above
URL=${MANTIS_URL:?}

# generate random 32 character alphanumeric string (upper and lowercase)
KEY=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)

# calculate signature
SIG=$(echo -n ${SECRET_KEY}${KEY}|md5sum)

# call actual cleanup page
curl "${URL}/plugin.php?page=DatabaseCleanup/cleanup&key=${KEY}&sig=${SIG:0:32}"
