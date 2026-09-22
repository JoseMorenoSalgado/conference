// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Keep the Conference card synchronized with the server schedule.
 *
 * The browser clock is offset against the server timestamp supplied by PHP.
 * The external meeting URL is never exposed here; the visible button points
 * only to the server-side join endpoint, which performs its own time check.
 *
 * @module     mod_conference/schedule
 * @copyright  2026 José Moreno Salgado
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const SECOND = 1000;
const STATUS_CLASSES = {
    scheduled: 'text-bg-secondary',
    live: 'text-bg-success',
    ended: 'text-bg-dark',
};

/**
 * Format seconds as hours, minutes, and seconds.
 *
 * Hours intentionally exceed 24 so the countdown stays compact for events
 * scheduled several days in advance.
 *
 * @param {number} seconds Remaining seconds.
 * @returns {string} Human-readable countdown.
 */
const formatRemaining = seconds => {
    const safeSeconds = Math.max(0, Math.ceil(seconds));
    const hours = Math.floor(safeSeconds / 3600);
    const minutes = Math.floor((safeSeconds % 3600) / 60);
    const remainingSeconds = safeSeconds % 60;
    const pad = value => String(value).padStart(2, '0');

    return `${pad(hours)}:${pad(minutes)}:${pad(remainingSeconds)}`;
};

/**
 * Initialise automatic conference state transitions.
 *
 * @param {Object} config Runtime configuration.
 * @param {string} config.elementId Conference card DOM id.
 * @param {number} config.serverTime Server UNIX timestamp at page render.
 * @param {number} config.startTime Scheduled start UNIX timestamp.
 * @param {number} config.endTime Optional end UNIX timestamp, or 0.
 * @param {string} config.countdownPrefix Localised countdown prefix.
 * @param {string} config.scheduledLabel Localised scheduled status.
 * @param {string} config.liveLabel Localised live status.
 * @param {string} config.endedLabel Localised ended status.
 * @param {string} config.liveAnnouncement Accessibility announcement for start.
 * @param {string} config.endedAnnouncement Accessibility announcement for end.
 */
export const init = config => {
    const root = document.getElementById(config.elementId);
    if (!root) {
        return;
    }

    const regions = {
        scheduled: root.querySelector('[data-region="scheduled"]'),
        live: root.querySelector('[data-region="live"]'),
        ended: root.querySelector('[data-region="ended"]'),
    };
    const badge = root.querySelector('[data-region="status-badge"]');
    const countdown = root.querySelector('[data-region="countdown"]');
    const announcement = root.querySelector('[data-region="announcement"]');
    const labels = {
        scheduled: config.scheduledLabel,
        live: config.liveLabel,
        ended: config.endedLabel,
    };
    const announcements = {
        live: config.liveAnnouncement,
        ended: config.endedAnnouncement,
    };

    const clientTimeAtInit = Date.now();
    const serverOffset = (config.serverTime * SECOND) - clientTimeAtInit;
    let currentState = null;
    let timerId = null;

    /**
     * Apply a visual state to the card.
     *
     * @param {string} state State name.
     * @param {boolean} announce Whether to announce the transition.
     */
    const setState = (state, announce) => {
        Object.entries(regions).forEach(([name, element]) => {
            if (element) {
                element.classList.toggle('d-none', name !== state);
            }
        });

        if (badge) {
            Object.values(STATUS_CLASSES).forEach(className => {
                badge.classList.remove(className);
            });
            badge.classList.add(STATUS_CLASSES[state]);
            badge.textContent = labels[state];
        }

        if (announce && announcement && announcements[state]) {
            announcement.textContent = announcements[state];
        }

        currentState = state;
    };

    /**
     * Synchronize UI with the calculated server time.
     */
    const update = () => {
        const serverNow = (Date.now() + serverOffset) / SECOND;
        let nextState = 'scheduled';

        if (config.endTime > 0 && serverNow >= config.endTime) {
            nextState = 'ended';
        } else if (serverNow >= config.startTime) {
            nextState = 'live';
        }

        if (nextState !== currentState) {
            setState(nextState, currentState !== null);
        }

        if (nextState === 'scheduled' && countdown) {
            const remaining = config.startTime - serverNow;
            countdown.textContent = `${config.countdownPrefix} ${formatRemaining(remaining)}`;
        }

        if (nextState === 'ended' || (nextState === 'live' && config.endTime <= 0)) {
            if (timerId !== null) {
                window.clearInterval(timerId);
                timerId = null;
            }
        }
    };

    update();

    if (currentState === 'scheduled' || (currentState === 'live' && config.endTime > 0)) {
        timerId = window.setInterval(update, SECOND);
    }
};
